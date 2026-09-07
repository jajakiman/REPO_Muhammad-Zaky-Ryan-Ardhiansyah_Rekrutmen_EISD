<?php

namespace Tests\Feature;

use App\Models\AccessibilityFeature;
use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OfficerHandlingTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $facility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
            'condition' => 'broken',
        ]);
        $category = IssueCategory::factory()->create();
        $reporter = User::factory()->create(['role' => 'reporter']);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);

        $report = AccessibilityReport::factory()->create([
            'reporter_id' => $reporter->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'status' => 'verified',
            'priority' => 'high',
            'officer_id' => $officer->id,
            'verified_at' => now(),
        ]);

        return compact('campus', 'area', 'location', 'facility', 'category', 'reporter', 'officer', 'report');
    }

    public function test_assigned_officer_can_start_handling_verified_report(): void
    {
        $data = $this->createSetup();

        $response = $this->actingAs($data['officer'])->post(route('officer.reports.start', $data['report']));

        $response->assertRedirect(route('officer.reports.show', $data['report']))
            ->assertSessionHas('success', 'Penanganan laporan telah dimulai.');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $data['report']->id,
            'status' => 'in_progress',
        ]);

        $fresh = $data['report']->fresh();
        $this->assertNotNull($fresh->handling_started_at);
    }

    public function test_other_officer_cannot_start_handling(): void
    {
        $data = $this->createSetup();

        $otherOfficer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $data['campus']->id,
            'campus_area_id' => $data['area']->id,
        ]);

        $response = $this->actingAs($otherOfficer)->post(route('officer.reports.start', $data['report']));

        $response->assertRedirect(route('officer.reports.show', $data['report']))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $data['report']->id,
            'status' => 'verified',
        ]);
    }

    public function test_assigned_officer_can_resolve_report_and_atomically_updates_facility_condition(): void
    {
        Storage::fake('report-photos');
        $data = $this->createSetup();

        // Put report in progress first
        $data['report']->update([
            'status' => 'in_progress',
            'handling_started_at' => now(),
        ]);

        $photo = UploadedFile::fake()->create('hasil_perbaikan.jpg', 600, 'image/jpeg');

        $payload = [
            'resolution_notes' => 'Ramp telah diperbaiki dan dipasangi pegangan tangan stainless steel baru.',
            'condition' => 'good',
            'resolution_photo' => $photo,
        ];

        $response = $this->actingAs($data['officer'])->post(route('officer.reports.resolve', $data['report']), $payload);

        $response->assertRedirect(route('officer.reports.show', $data['report']))
            ->assertSessionHas('success', 'Laporan berhasil diselesaikan dan kondisi fasilitas diperbarui.');

        // Report is resolved
        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $data['report']->id,
            'status' => 'resolved',
            'resolution_notes' => 'Ramp telah diperbaiki dan dipasangi pegangan tangan stainless steel baru.',
        ]);

        $freshReport = $data['report']->fresh();
        $this->assertNotNull($freshReport->resolved_at);
        $this->assertNotNull($freshReport->resolution_photo_path);
        Storage::disk('report-photos')->assertExists($freshReport->resolution_photo_path);

        // Facility condition is atomically updated to good!
        $this->assertDatabaseHas('location_accessibility_features', [
            'id' => $data['facility']->id,
            'condition' => 'good',
        ]);

        $freshFacility = $data['facility']->fresh();
        $this->assertNotNull($freshFacility->last_checked_at);
    }

    public function test_resolution_validation_requires_notes_and_valid_condition(): void
    {
        $data = $this->createSetup();
        $data['report']->update(['status' => 'in_progress']);

        $response = $this->actingAs($data['officer'])->post(route('officer.reports.resolve', $data['report']), [
            'resolution_notes' => '',
            'condition' => 'invalid_condition',
        ]);

        $response->assertSessionHasErrors(['resolution_notes', 'condition']);
    }

    public function test_officer_can_view_history_of_terminal_reports_in_their_area(): void
    {
        $data = $this->createSetup();

        // Terminal reports: resolved, rejected, cancelled
        $resolved = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $data['facility']->id,
            'reporter_id' => $data['reporter']->id,
            'issue_category_id' => $data['category']->id,
            'status' => 'resolved',
            'officer_id' => $data['officer']->id,
            'resolution_notes' => 'Telah selesai diperbaiki',
        ]);

        $rejected = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $data['facility']->id,
            'reporter_id' => $data['reporter']->id,
            'issue_category_id' => $data['category']->id,
            'status' => 'rejected',
            'officer_id' => $data['officer']->id,
            'rejection_reason' => 'Bukan wewenang',
        ]);

        $cancelled = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $data['facility']->id,
            'reporter_id' => $data['reporter']->id,
            'issue_category_id' => $data['category']->id,
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($data['officer'])->get(route('officer.history.index'));

        $response->assertOk()
            ->assertSee('Riwayat Penanganan Area')
            ->assertSee($resolved->report_code)
            ->assertSee($rejected->report_code)
            ->assertSee($cancelled->report_code);
    }
}
