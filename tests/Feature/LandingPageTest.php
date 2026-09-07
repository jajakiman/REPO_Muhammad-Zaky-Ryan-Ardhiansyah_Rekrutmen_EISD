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

    public function test_metric_workflow_and_footer_content_use_balanced_centered_layouts(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('stat-grid', false)
            ->assertSee('workflow-grid', false)
            ->assertSee('workflow-card', false)
            ->assertSee('items-center', false)
            ->assertSee('text-center', false)
            ->assertSee('site-footer', false)
            ->assertSee('rounded-2xl shadow-sm border border-slate-200', false)
            ->assertSee(route('map.index'))
            ->assertSee(route('home').'#faq-title', false)
            ->assertSee('Sistem Pelaporan Fasilitas Kampus');
    }

    public function test_landing_numbers_follow_the_semantic_three_plus_one_palette(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(3, substr_count($html, 'stat-number text-4xl lg:text-5xl font-black text-navy-900'));
        $this->assertSame(1, substr_count($html, 'stat-number text-4xl lg:text-5xl font-black text-emerald-800'));
        $this->assertSame(4, substr_count($html, 'workflow-number mx-auto w-10 h-10 rounded-xl bg-navy-50 text-navy-900'));
    }

    public function test_motion_dev_script_is_integrated_with_reduced_motion_fallback(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('cdn.jsdelivr.net/npm/motion', false)
            ->assertSee('motion-interactive.js', false);

        $this->assertFileExists(public_path('js/motion-interactive.js'));

        $scriptContent = file_get_contents(public_path('js/motion-interactive.js'));
        $this->assertStringContainsString('prefers-reduced-motion', $scriptContent);
        $this->assertStringContainsString('Motion', $scriptContent);
    }
}
