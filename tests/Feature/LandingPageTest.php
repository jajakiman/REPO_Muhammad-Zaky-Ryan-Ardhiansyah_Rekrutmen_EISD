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

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_displays_real_database_stats_and_sdg_narrative(): void
    {
        $campus = Campus::factory()->create(['name' => 'Telkom University Bandung', 'is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => true]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'is_active' => true]);
        $feature = AccessibilityFeature::factory()->create(['name' => 'Ramp Aksesibel', 'is_active' => true]);
        $laf = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);
        $cat = IssueCategory::factory()->create();

        AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $laf->id,
            'issue_category_id' => $cat->id,
            'status' => 'resolved',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('AksesLoka')
            ->assertSee('SDGs 11')
            ->assertSee('Alur Kerja Penanganan')
            ->assertSee('Lihat peta')
            ->assertSee(route('map.index'))
            ->assertSee('Statistik Operasional')
            ->assertSee('Kampus Terpetakan')
            ->assertSee('Lokasi Kampus')
            ->assertSee('Fasilitas Terdata')
            ->assertSee('Laporan Diselesaikan');
    }
}
