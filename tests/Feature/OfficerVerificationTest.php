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
use Tests\TestCase;

class OfficerVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAreaReport(CampusArea $area, array $reportAttributes = []): AccessibilityReport
    {
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $facility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);
        $category = IssueCategory::factory()->create();
        $reporter = User::factory()->create(['role' => 'reporter']);

        return AccessibilityReport::factory()->create(array_merge([
            'reporter_id' => $reporter->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'status' => 'submitted',
            'officer_id' => null,
            'priority' => null,
        ], $reportAttributes));
    }

    public function test_officer_can_view_queue_only_for_assigned_area(): void
    {
        $campus = Campus::factory()->create();
        $area1 = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Timur']);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Barat']);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area1->id,
        ]);

        $reportInArea = $this->createAreaReport($area1, ['description' => 'Laporan di Area Timur']);
        $reportOutsideArea = $this->createAreaReport($area2, ['description' => 'Laporan di Area Barat']);

        $response = $this->actingAs($officer)->get(route('officer.queue.index'));

        $response->assertOk()
            ->assertSee('Antrean Laporan Area')
            ->assertSee('Semua Status')
            ->assertSee('Menunggu Verifikasi')
            ->assertDontSee('(Submitted)', false)
            ->assertSee($area1->name)
            ->assertSee($reportInArea->report_code)
            ->assertDontSee($reportOutsideArea->report_code);

        // Filter by specific status
        $this->actingAs($officer)->get(route('officer.queue.index', ['status' => 'verified']))
            ->assertOk()
            ->assertDontSee($reportInArea->report_code);

        // Filter by all statuses
        $this->actingAs($officer)->get(route('officer.queue.index', ['status' => 'all']))
            ->assertOk()
            ->assertSee($reportInArea->report_code);
    }

    public function test_officer_cannot_view_report_detail_outside_their_area(): void
    {
        $campus = Campus::factory()->create();
        $area1 = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus->id]);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area1->id,
        ]);

        $reportOutside = $this->createAreaReport($area2);

        $this->actingAs($officer)->get(route('officer.reports.show', $reportOutside))
            ->assertForbidden();
    }

    public function test_officer_can_verify_and_claim_report_with_priority(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);

        $report = $this->createAreaReport($area);

        $response = $this->actingAs($officer)->post(route('officer.reports.verify', $report), [
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('officer.reports.show', $report))
            ->assertSessionHas('success', 'Laporan berhasil diverifikasi dan menjadi tanggung jawab Anda.');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $report->id,
            'officer_id' => $officer->id,
            'status' => 'verified',
            'priority' => 'high',
        ]);

        $fresh = $report->fresh();
        $this->assertNotNull($fresh->verified_at);
    }

    public function test_officer_cannot_claim_already_claimed_or_processed_report(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);

        $officer1 = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);

        $officer2 = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);

        $report = $this->createAreaReport($area, [
            'status' => 'verified',
            'officer_id' => $officer1->id,
            'priority' => 'medium',
        ]);

        // Officer 2 attempts to verify/claim
        $response = $this->actingAs($officer2)->post(route('officer.reports.verify', $report), [
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('officer.reports.show', $report))
            ->assertSessionHas('error', 'Laporan telah diproses oleh Petugas lain.');

        // Report still belongs to officer 1
        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $report->id,
            'officer_id' => $officer1->id,
            'priority' => 'medium',
        ]);
    }

    public function test_officer_can_reject_report_with_required_reason(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);

        $report = $this->createAreaReport($area);

        // Validation fails if reason is empty
        $this->actingAs($officer)->post(route('officer.reports.reject', $report), [
            'rejection_reason' => '',
        ])->assertSessionHasErrors('rejection_reason');

        // Successful rejection
        $response = $this->actingAs($officer)->post(route('officer.reports.reject', $report), [
            'rejection_reason' => 'Bukan fasilitas kampus, berada di luar area wewenang.',
        ]);

        $response->assertRedirect(route('officer.reports.show', $report))
            ->assertSessionHas('success', 'Laporan berhasil ditolak.');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $report->id,
            'officer_id' => $officer->id,
            'status' => 'rejected',
            'rejection_reason' => 'Bukan fasilitas kampus, berada di luar area wewenang.',
        ]);

        $fresh = $report->fresh();
        $this->assertNotNull($fresh->verified_at);
    }
}
