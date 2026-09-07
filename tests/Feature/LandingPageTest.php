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

    public function test_display_typography_uses_pp_editorial_with_a_resilient_fallback(): void
    {
        $response = $this->get(route('home'));
        $css = file_get_contents(public_path('css/app.css'));

        $response->assertOk()
            ->assertSee('fontFamily', false)
            ->assertSee('PP Editorial New', false)
            ->assertSee('PP Neue Montreal', false);
        $this->assertStringContainsString('--font-display: "PP Editorial New"', $css);
        $this->assertStringContainsString('--font-sans: "PP Neue Montreal"', $css);
        $this->assertStringContainsString('font-family: var(--font-display)', $css);
        $this->assertStringContainsString('font-display: swap', $css);
    }

    public function test_hero_uses_dual_type_and_a_real_map_showcase(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Temukan &amp; Pantau', false)
            ->assertSee('Fasilitas Kampus')
            ->assertSee('font-sans', false)
            ->assertSee('font-display italic', false)
            ->assertSee('Pemetaan Kampus Bandung')
            ->assertSee(route('map.index'))
            ->assertDontSee('Play trailer');
    }

    public function test_landing_page_answers_product_specific_questions_in_an_open_grid(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pertanyaan yang Sering Diajukan')
            ->assertSee('faq-grid', false)
            ->assertSee('Apakah pengunjung tanpa akun bisa melihat peta dan fasilitas?')
            ->assertSee('Apakah sistem ini melacak koordinat GPS pengguna?')
            ->assertSee('Apakah AksesLoka merupakan audit aksesibilitas resmi?')
            ->assertDontSee('<details', false);
    }
}
