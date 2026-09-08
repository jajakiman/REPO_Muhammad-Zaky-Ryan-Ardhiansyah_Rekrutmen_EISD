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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporter_dashboard_displays_real_metrics_and_five_recent_reports(): void
    {
        $reporter = User::factory()->create(['role' => 'reporter']);
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $laf = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);
        $category = IssueCategory::factory()->create();

        // Create 6 reports for reporter
        for ($i = 1; $i <= 6; $i++) {
            AccessibilityReport::factory()->create([
                'reporter_id' => $reporter->id,
                'location_accessibility_feature_id' => $laf->id,
                'issue_category_id' => $category->id,
                'description' => "Laporan ke-$i",
                'status' => $i === 1 ? 'resolved' : 'submitted',
                'created_at' => now()->subMinutes(10 - $i),
            ]);
        }

        $response = $this->actingAs($reporter)->get(route('reporter.dashboard'));

        $response->assertOk()
            ->assertSee('Total Laporan')
            ->assertSee('6')
            ->assertSee('Laporan Aktif')
            ->assertSee('5')
            ->assertSee('Laporan Selesai')
            ->assertSee('1')
            ->assertSee('Buat Laporan')
            ->assertSee('Riwayat Laporan')
            ->assertSee('font-display', false)
            ->assertDontSee('Pelaporan Cepat')
            ->assertSee('Laporan ke-6')
            ->assertDontSee('Laporan ke-1'); // Cap at 5 most recent
    }

    public function test_new_reporter_dashboard_explains_the_reporting_flow_and_offers_a_real_start_action(): void
    {
        $reporter = User::factory()->create(['role' => 'reporter']);

        $this->actingAs($reporter)->get(route('reporter.dashboard'))
            ->assertOk()
            ->assertSee('reporter-dashboard-intro', false)
            ->assertSee('text-slate-600', false)
            ->assertSee('reporter-metric-card', false)
            ->assertSee('Buat Laporan')
            ->assertSee('Riwayat Laporan')
            ->assertSee('Buat Laporan Pertama')
            ->assertSee('href="'.route('reporter.reports.create').'"', false);
    }

    public function test_officer_dashboard_displays_area_metrics_and_queue_snapshot(): void
    {
        $campus = Campus::factory()->create(['name' => 'Telkom University']);
        $area1 = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Satu']);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Dua']);

        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area1->id,
        ]);

        $loc1 = CampusLocation::factory()->create(['campus_area_id' => $area1->id]);
        $loc2 = CampusLocation::factory()->create(['campus_area_id' => $area2->id]);
        $feature = AccessibilityFeature::factory()->create();
        $laf1 = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $loc1->id, 'accessibility_feature_id' => $feature->id]);
        $laf2 = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $loc2->id, 'accessibility_feature_id' => $feature->id]);
        $cat = IssueCategory::factory()->create();

        // Reports in area 1
        AccessibilityReport::factory()->create(['location_accessibility_feature_id' => $laf1->id, 'issue_category_id' => $cat->id, 'status' => 'submitted', 'description' => 'Kendala Area 1']);
        AccessibilityReport::factory()->create(['location_accessibility_feature_id' => $laf1->id, 'issue_category_id' => $cat->id, 'status' => 'in_progress', 'officer_id' => $officer->id]);
        AccessibilityReport::factory()->create(['location_accessibility_feature_id' => $laf1->id, 'issue_category_id' => $cat->id, 'status' => 'resolved', 'officer_id' => $officer->id]);

        // Report in area 2 (should not be in metrics or list)
        AccessibilityReport::factory()->create(['location_accessibility_feature_id' => $laf2->id, 'issue_category_id' => $cat->id, 'status' => 'submitted', 'description' => 'Kendala Area 2']);

        $response = $this->actingAs($officer)->get(route('officer.dashboard'));

        $response->assertOk()
            ->assertSee('Telkom University')
            ->assertSee('Area Satu')
            ->assertSee('Kendala Area 1')
            ->assertDontSee('Kendala Area 2');
    }

    public function test_admin_dashboard_shows_aggregate_counts_and_recent_cross_campus_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus = Campus::factory()->create(['is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => true]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'is_active' => true]);
        $feature = AccessibilityFeature::factory()->create(['is_active' => true]);
        $laf = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id, 'accessibility_feature_id' => $feature->id]);
        $cat = IssueCategory::factory()->create();

        $officer = User::factory()->create(['role' => 'officer', 'campus_area_id' => $area->id, 'is_active' => true]);

        $report = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $laf->id,
            'issue_category_id' => $cat->id,
            'status' => 'submitted',
            'description' => 'Laporan Lintas Kampus',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Kampus Aktif')
            ->assertSee('Lokasi Terdata')
            ->assertSee('Fasilitas Terdata')
            ->assertSee('Petugas Aktif')
            ->assertSee('Laporan Lintas Kampus');
    }

    public function test_admin_can_monitor_all_reports_with_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus1 = Campus::factory()->create(['name' => 'Kampus Alpha']);
        $area1 = CampusArea::factory()->create(['campus_id' => $campus1->id]);
        $loc1 = CampusLocation::factory()->create(['campus_area_id' => $area1->id]);
        $feat = AccessibilityFeature::factory()->create();
        $laf1 = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $loc1->id, 'accessibility_feature_id' => $feat->id]);
        $cat = IssueCategory::factory()->create();

        $report1 = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $laf1->id,
            'issue_category_id' => $cat->id,
            'status' => 'submitted',
            'description' => 'Laporan Kampus Alpha',
        ]);

        $campus2 = Campus::factory()->create(['name' => 'Kampus Beta']);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus2->id]);
        $loc2 = CampusLocation::factory()->create(['campus_area_id' => $area2->id]);
        $laf2 = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $loc2->id, 'accessibility_feature_id' => $feat->id]);

        $report2 = AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $laf2->id,
            'issue_category_id' => $cat->id,
            'status' => 'resolved',
            'description' => 'Laporan Kampus Beta',
        ]);

        // All reports visible to admin
        $this->actingAs($admin)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Monitoring Seluruh Laporan')
            ->assertSee($report1->report_code)
            ->assertSee($report2->report_code);

        // Filter by campus
        $this->actingAs($admin)->get(route('admin.reports.index', ['campus_id' => $campus1->id]))
            ->assertOk()
            ->assertSee($report1->report_code)
            ->assertDontSee($report2->report_code);

        // Read-only report detail
        $this->actingAs($admin)->get(route('admin.reports.show', $report1))
            ->assertOk()
            ->assertSee($report1->report_code)
            ->assertDontSee('Verifikasi & Klaim Laporan'); // Admin is read-only
    }
}
