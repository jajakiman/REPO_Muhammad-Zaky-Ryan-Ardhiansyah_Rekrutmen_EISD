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

class ReportLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_report_closed_loop_lifecycle_across_all_roles_and_public_map(): void
    {
        Storage::fake('report-photos');

        // 1. Setup Master Data (Campus, Area, Location, Feature, Pivot with broken condition)
        $campus = Campus::factory()->create(['name' => 'Telkom University Bandung', 'is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Kawasan Kuliah Bersama', 'is_active' => true]);
        $location = CampusLocation::factory()->create([
            'campus_area_id' => $area->id,
            'name' => 'Gedung Manterawu',
            'location_type' => 'building',
            'accessibility_status' => 'partially_accessible',
            'is_active' => true,
        ]);
        $feature = AccessibilityFeature::factory()->create(['name' => 'Ramp Utama', 'is_active' => true]);
        $facility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
            'availability_status' => 'available',
            'condition' => 'broken',
            'notes' => 'Ramp miring dan pegangan rusak',
        ]);
        $category = IssueCategory::factory()->create(['name' => 'Fasilitas Rusak', 'is_active' => true]);

        // Create Admin & Area Officer
        $admin = User::factory()->create(['role' => 'admin']);
        $officer = User::factory()->create([
            'role' => 'officer',
            'name' => 'Budi Santoso',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
            'is_active' => true,
        ]);

        // 2. Public Visitor flow: Visits Home, navigates to Map and Location Detail
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('AksesLoka')
            ->assertSee('Lihat peta');

        $this->get(route('map.index', ['q' => 'Manterawu']))
            ->assertOk()
            ->assertSee('Gedung Manterawu');

        $detailResponse = $this->get(route('locations.show', $location));
        $detailResponse->assertOk()
            ->assertSee('Ramp Utama')
            ->assertSee('Rusak')
            ->assertSee('Laporkan Masalah')
            ->assertSee(route('login'));

        // 3. Visitor registers as Reporter
        $registerResponse = $this->post(route('register'), [
            'name' => 'Ahmad Pelapor',
            'email' => 'ahmad@student.telkomuniversity.ac.id',
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'affiliation_type' => 'student',
            'campus_id' => $campus->id,
        ]);
        $registerResponse->assertRedirect(route('login'));

        // Reporter logs in
        $loginResponse = $this->post(route('login'), [
            'email' => 'ahmad@student.telkomuniversity.ac.id',
            'password' => 'Rahasia123!',
        ]);
        $loginResponse->assertRedirect(route('reporter.dashboard'));

        $reporter = User::where('email', 'ahmad@student.telkomuniversity.ac.id')->firstOrFail();
        $this->assertSame('reporter', $reporter->role);

        // 4. Reporter submits a problem report with a photo
        $photo = UploadedFile::fake()->create('foto_kendala.jpg', 500, 'image/jpeg');

        $submitResponse = $this->actingAs($reporter)->post(route('reporter.reports.store'), [
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'description' => 'Besi pegangan ramp lepas dan lantai licin membahayakan pengguna kursi roda.',
            'photo' => $photo,
        ]);

        $report = AccessibilityReport::where('reporter_id', $reporter->id)->firstOrFail();
        $submitResponse->assertRedirect(route('reporter.reports.show', $report));
        $this->assertSame('submitted', $report->status);
        $this->assertNull($report->officer_id);

        // Reporter sees report in their list and detail
        $this->actingAs($reporter)->get(route('reporter.reports.index'))
            ->assertOk()
            ->assertSee($report->report_code);

        // 5. Admin monitors all reports across campuses
        auth()->logout();
        $this->actingAs($admin)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee($report->report_code)
            ->assertSee('Ahmad Pelapor');

        // 6. Officer checks area queue, verifies and claims the report
        auth()->logout();
        $queueResponse = $this->actingAs($officer)->get(route('officer.queue.index'));
        $queueResponse->assertOk()
            ->assertSee($report->report_code);

        $verifyResponse = $this->actingAs($officer)->post(route('officer.reports.verify', $report), [
            'priority' => 'high',
        ]);
        $verifyResponse->assertRedirect(route('officer.reports.show', $report));

        $report->refresh();
        $this->assertSame('verified', $report->status);
        $this->assertSame($officer->id, $report->officer_id);
        $this->assertSame('high', $report->priority);

        // 7. Officer starts handling
        $startResponse = $this->actingAs($officer)->post(route('officer.reports.start', $report));
        $startResponse->assertRedirect(route('officer.reports.show', $report));

        $report->refresh();
        $this->assertSame('in_progress', $report->status);
        $this->assertNotNull($report->handling_started_at);

        // 8. Officer resolves report with resolution notes, photo, and updates condition to good
        $resPhoto = UploadedFile::fake()->create('hasil_perbaikan.jpg', 600, 'image/jpeg');

        $resolveResponse = $this->actingAs($officer)->post(route('officer.reports.resolve', $report), [
            'resolution_notes' => 'Pemasangan kembali besi pegangan ramp selesai dan lantai telah dilapisi anti-slip.',
            'condition' => 'good',
            'resolution_photo' => $resPhoto,
        ]);
        $resolveResponse->assertRedirect(route('officer.reports.show', $report));

        $report->refresh();
        $this->assertSame('resolved', $report->status);
        $this->assertNotNull($report->resolved_at);

        // Facility condition in locationAccessibilityFeature is atomically updated to good!
        $facility->refresh();
        $this->assertSame('good', $facility->condition);

        // 9. Reporter checks their report and sees resolved status and resolution notes
        auth()->logout();
        $this->actingAs($reporter)->get(route('reporter.reports.show', $report))
            ->assertOk()
            ->assertSee('Selesai')
            ->assertSee('Pemasangan kembali besi pegangan ramp selesai');

        // 10. Public Visitor views the facility on the map and sees updated condition (Baik / good)
        auth()->logout();
        $this->get(route('locations.show', $location))
            ->assertOk()
            ->assertSee('Ramp Utama')
            ->assertSee('Baik')
            ->assertDontSee('Rusak');
    }
}
