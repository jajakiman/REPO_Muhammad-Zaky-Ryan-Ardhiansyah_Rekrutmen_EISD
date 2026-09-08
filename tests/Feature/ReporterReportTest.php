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

class ReporterReportTest extends TestCase
{
    use RefreshDatabase;

    private function createFacility(): LocationAccessibilityFeature
    {
        $campus = Campus::factory()->create(['is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => true]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'is_active' => true]);
        $feature = AccessibilityFeature::factory()->create(['is_active' => true]);

        return LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);
    }

    public function test_guests_and_other_roles_cannot_access_report_submission_routes(): void
    {
        $facility = $this->createFacility();
        $officer = User::factory()->create(['role' => 'officer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $endpoints = [
            ['get', route('reporter.reports.index')],
            ['get', route('reporter.reports.create', ['facility_id' => $facility->id])],
            ['post', route('reporter.reports.store')],
        ];

        foreach ($endpoints as [$method, $uri]) {
            // Guest redirected to login
            $this->{$method}($uri)->assertRedirect(route('login'));

            // Other roles forbidden (403)
            auth()->logout();
            $this->actingAs($officer)->{$method}($uri)->assertForbidden();
            auth()->logout();
            $this->actingAs($admin)->{$method}($uri)->assertForbidden();
            auth()->logout();
        }
    }

    public function test_reporter_can_view_report_creation_form_for_active_facility(): void
    {
        $facility = $this->createFacility();
        $category = IssueCategory::factory()->create(['name' => 'Fasilitas Rusak', 'is_active' => true]);
        $reporter = User::factory()->create(['role' => 'reporter']);

        $response = $this->actingAs($reporter)->get(route('reporter.reports.create', ['facility_id' => $facility->id]));

        $response->assertOk()
            ->assertSee('Buat Laporan Masalah')
            ->assertSee($facility->accessibilityFeature->name)
            ->assertSee($facility->campusLocation->name)
            ->assertSee('Fasilitas Rusak');
    }

    public function test_reporter_can_open_report_form_directly_and_choose_an_active_facility(): void
    {
        $facility = $this->createFacility();
        $facility->campusLocation->update(['name' => 'Gedung Tokong Nanas']);
        $facility->accessibilityFeature->update(['name' => 'Ramp']);
        IssueCategory::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create(['role' => 'reporter']))
            ->get(route('reporter.reports.create'))
            ->assertOk()
            ->assertSee('name="location_accessibility_feature_id"', false)
            ->assertSee('Gedung Tokong Nanas - Ramp')
            ->assertSee('Foto Bukti (Opsional)');
    }

    public function test_report_form_preselects_a_facility_from_the_map(): void
    {
        $facility = $this->createFacility();
        IssueCategory::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create(['role' => 'reporter']))
            ->get(route('reporter.reports.create', ['facility_id' => $facility->id]))
            ->assertOk()
            ->assertSee('value="'.$facility->id.'" selected', false);
    }

    public function test_report_form_shows_an_actionable_empty_state_without_active_facilities(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'reporter']))
            ->get(route('reporter.reports.create'))
            ->assertOk()
            ->assertSee('empty-state', false)
            ->assertSee('Belum ada fasilitas yang dapat dilaporkan')
            ->assertSee(route('map.index'))
            ->assertDontSee('Kirim Laporan');
    }

    public function test_reporter_cannot_create_report_for_inactive_facility_hierarchy(): void
    {
        $campus = Campus::factory()->create(['is_active' => false]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $facility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);

        $reporter = User::factory()->create(['role' => 'reporter']);

        $this->actingAs($reporter)
            ->get(route('reporter.reports.create', ['facility_id' => $facility->id]))
            ->assertNotFound();
    }

    public function test_reporter_can_submit_valid_report_with_optional_photo(): void
    {
        Storage::fake('report-photos');

        $facility = $this->createFacility();
        $category = IssueCategory::factory()->create(['is_active' => true]);
        $reporter = User::factory()->create(['role' => 'reporter']);

        $photo = UploadedFile::fake()->create('laporan.jpg', 500, 'image/jpeg');

        $payload = [
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'description' => 'Pegangan tangan ramp di sisi kiri patah dan tajam.',
            'photo' => $photo,
        ];

        $response = $this->actingAs($reporter)->post(route('reporter.reports.store'), $payload);

        $this->assertDatabaseHas('accessibility_reports', [
            'reporter_id' => $reporter->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'description' => 'Pegangan tangan ramp di sisi kiri patah dan tajam.',
            'status' => 'submitted',
            'priority' => null,
            'officer_id' => null,
        ]);

        $report = AccessibilityReport::firstOrFail();
        $this->assertNotNull($report->photo_path);
        Storage::disk('report-photos')->assertExists($report->photo_path);

        $this->assertMatchesRegularExpression('/^RPT-\d{8}-[A-Z0-9]{4}$/', $report->report_code);

        $response->assertRedirect(route('reporter.reports.show', $report))
            ->assertSessionHas('success', 'Laporan masalah berhasil dibuat.');

        $this->actingAs($reporter)->get(route('reporter.reports.show', $report))
            ->assertOk()
            ->assertSee($report->photo_url)
            ->assertSee('loading="lazy"', false)
            ->assertSee('decoding="async"', false);

        $this->get($report->photo_url)->assertOk();
    }

    public function test_report_validation_enforces_required_fields_and_photo_rules(): void
    {
        $facility = $this->createFacility();
        $reporter = User::factory()->create(['role' => 'reporter']);

        // Invalid file format & size
        $badFile = UploadedFile::fake()->create('dokumen.pdf', 3000, 'application/pdf');

        $response = $this->actingAs($reporter)->post(route('reporter.reports.store'), [
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => 999999,
            'description' => '',
            'photo' => $badFile,
        ]);

        $response->assertSessionHasErrors(['issue_category_id', 'description', 'photo']);
    }

    public function test_reporter_can_only_view_their_own_reports(): void
    {
        $facility = $this->createFacility();
        $category = IssueCategory::factory()->create();
        $reporter1 = User::factory()->create(['role' => 'reporter']);
        $reporter2 = User::factory()->create(['role' => 'reporter']);

        $report1 = AccessibilityReport::factory()->create([
            'reporter_id' => $reporter1->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'description' => 'Laporan dari pelapor 1',
        ]);

        $report2 = AccessibilityReport::factory()->create([
            'reporter_id' => $reporter2->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'description' => 'Laporan dari pelapor 2',
        ]);

        // Reporter 1 sees report 1 and not report 2 in index
        $indexResponse = $this->actingAs($reporter1)->get(route('reporter.reports.index'));
        $indexResponse->assertOk()
            ->assertSee($report1->report_code)
            ->assertDontSee($report2->report_code);

        // Reporter 1 can view report 1
        $this->actingAs($reporter1)->get(route('reporter.reports.show', $report1))
            ->assertOk()
            ->assertSee($report1->report_code);

        // Reporter 1 cannot view report 2 (403)
        $this->actingAs($reporter1)->get(route('reporter.reports.show', $report2))
            ->assertForbidden();
    }

    public function test_reporter_can_cancel_submitted_unclaimed_report(): void
    {
        $facility = $this->createFacility();
        $category = IssueCategory::factory()->create();
        $reporter = User::factory()->create(['role' => 'reporter']);

        $report = AccessibilityReport::factory()->create([
            'reporter_id' => $reporter->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'status' => 'submitted',
            'officer_id' => null,
        ]);

        $this->actingAs($reporter)->get(route('reporter.reports.show', $report))
            ->assertOk()
            ->assertSee('data-cancel-report-dialog', false)
            ->assertSee('Konfirmasi Pembatalan Laporan')
            ->assertSee('badge-submitted', false)
            ->assertDontSee('onsubmit="return confirm', false);

        $response = $this->actingAs($reporter)->patch(route('reporter.reports.cancel', $report));

        $response->assertRedirect(route('reporter.reports.show', $report))
            ->assertSessionHas('success', 'Laporan berhasil dibatalkan.');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $report->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_report_status_badges_use_distinct_semantic_colors(): void
    {
        $css = file_get_contents(public_path('css/app.css'));

        $this->assertStringContainsString('.badge-submitted', $css);
        $this->assertStringContainsString('.badge-verified', $css);
        $this->assertStringContainsString('.badge-in_progress', $css);
        $this->assertStringContainsString('.badge-resolved', $css);
        $this->assertStringContainsString('.badge-rejected', $css);
        $this->assertStringContainsString('.badge-cancelled', $css);

        // Verify that submitted is amber/yellow, not gray/neutral
        $this->assertStringContainsString('.badge-submitted { background: var(--amber-50)', $css);
    }

    public function test_reporter_cannot_cancel_already_processed_or_claimed_report(): void
    {
        $facility = $this->createFacility();
        $category = IssueCategory::factory()->create();
        $reporter = User::factory()->create(['role' => 'reporter']);
        $officer = User::factory()->create(['role' => 'officer']);

        $report = AccessibilityReport::factory()->create([
            'reporter_id' => $reporter->id,
            'location_accessibility_feature_id' => $facility->id,
            'issue_category_id' => $category->id,
            'status' => 'verified',
            'officer_id' => $officer->id,
        ]);

        $response = $this->actingAs($reporter)->patch(route('reporter.reports.cancel', $report));

        $response->assertRedirect(route('reporter.reports.show', $report))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('accessibility_reports', [
            'id' => $report->id,
            'status' => 'verified',
        ]);
    }

    public function test_reporter_with_campus_affiliation_only_sees_facilities_from_their_campus_in_report_form(): void
    {
        $telkomCampus = Campus::factory()->create(['name' => 'Telkom University', 'is_active' => true]);
        $telkomArea = CampusArea::factory()->create(['campus_id' => $telkomCampus->id, 'is_active' => true]);
        $telkomLocation = CampusLocation::factory()->create(['campus_area_id' => $telkomArea->id, 'name' => 'Gedung Tokong Nanas', 'is_active' => true]);
        $feature1 = AccessibilityFeature::factory()->create(['name' => 'Ramp Utama', 'is_active' => true]);
        $telkomFacility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $telkomLocation->id,
            'accessibility_feature_id' => $feature1->id,
        ]);

        $upiCampus = Campus::factory()->create(['name' => 'UPI Bandung', 'is_active' => true]);
        $upiArea = CampusArea::factory()->create(['campus_id' => $upiCampus->id, 'is_active' => true]);
        $upiLocation = CampusLocation::factory()->create(['campus_area_id' => $upiArea->id, 'name' => 'Gymnasium UPI', 'is_active' => true]);
        $feature2 = AccessibilityFeature::factory()->create(['name' => 'Toilet Difabel', 'is_active' => true]);
        $upiFacility = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $upiLocation->id,
            'accessibility_feature_id' => $feature2->id,
        ]);

        IssueCategory::factory()->create(['is_active' => true]);

        $telkomStudent = User::factory()->create([
            'role' => 'reporter',
            'affiliation_type' => 'student',
            'campus_id' => $telkomCampus->id,
        ]);

        $response = $this->actingAs($telkomStudent)->get(route('reporter.reports.create'));
        $response->assertOk()
            ->assertSee('Gedung Tokong Nanas - Ramp Utama')
            ->assertSee('Telkom University')
            ->assertDontSee('Gymnasium UPI - Toilet Difabel');

        // Accessing pre-selected facility from foreign campus returns 404
        $this->actingAs($telkomStudent)
            ->get(route('reporter.reports.create', ['facility_id' => $upiFacility->id]))
            ->assertNotFound();

        // Submitting report for foreign campus facility fails validation
        $category = IssueCategory::first();
        $this->actingAs($telkomStudent)
            ->post(route('reporter.reports.store'), [
                'location_accessibility_feature_id' => $upiFacility->id,
                'issue_category_id' => $category->id,
                'description' => 'Mencoba melaporkan kampus lain yang bukan afiliasi.',
            ])
            ->assertSessionHasErrors(['location_accessibility_feature_id']);

        // Submitting report for own campus facility succeeds
        $this->actingAs($telkomStudent)
            ->post(route('reporter.reports.store'), [
                'location_accessibility_feature_id' => $telkomFacility->id,
                'issue_category_id' => $category->id,
                'description' => 'Pegangan ramp di Tokong Nanas kendor.',
            ])
            ->assertSessionHasNoErrors();
    }
}
