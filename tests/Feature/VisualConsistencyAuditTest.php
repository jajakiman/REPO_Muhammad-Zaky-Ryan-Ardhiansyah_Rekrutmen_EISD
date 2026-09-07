<?php

namespace Tests\Feature;

use App\Models\AccessibilityFeature;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualConsistencyAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_major_routes_follow_their_exact_layout_contracts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $laf = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id, 'accessibility_feature_id' => $feature->id]);
        $officer = User::factory()->create(['role' => 'officer', 'campus_area_id' => $area->id]);
        $reporter = User::factory()->create(['role' => 'reporter', 'campus_id' => $campus->id]);

        // 1. Public pages (layouts.app)
        $publicRoutes = [route('home'), route('map.index'), route('locations.show', $location)];
        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $response->assertOk()
                ->assertSee('site-footer', false)
                ->assertDontSee('dashboard-sidebar', false);
        }

        // 2. Auth pages (layouts.auth)
        $authRoutes = [route('login'), route('register')];
        foreach ($authRoutes as $route) {
            $response = $this->get($route);
            $response->assertOk()
                ->assertSee('min-h-[100dvh]', false)
                ->assertSee('Kembali ke Halaman Utama')
                ->assertDontSee('site-header', false)
                ->assertDontSee('site-footer', false);
        }

        // 3. Authenticated actor pages (layouts.dashboard)
        $actorRoutePairs = [
            [$reporter, route('reporter.dashboard')],
            [$reporter, route('reporter.reports.index')],
            [$reporter, route('reporter.profile.edit')],
            [$officer, route('officer.dashboard')],
            [$officer, route('officer.queue.index')],
            [$officer, route('officer.history.index')],
            [$admin, route('admin.dashboard')],
            [$admin, route('admin.campuses.index')],
            [$admin, route('admin.reports.index')],
            [$admin, route('admin.features.index')],
            [$admin, route('admin.officers.index')],
        ];

        foreach ($actorRoutePairs as [$user, $route]) {
            $response = $this->actingAs($user)->get($route);
            $response->assertOk()
                ->assertSee('dashboard-sidebar', false)
                ->assertSee('sidebar-navigation', false)
                ->assertDontSee('site-footer', false);
        }
    }

    public function test_no_pages_contain_dead_href_or_prohibited_emojis(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);

        $routesToAudit = [
            route('home'),
            route('map.index'),
            route('locations.show', $location),
            route('login'),
            route('register'),
        ];

        foreach ($routesToAudit as $route) {
            $response = $this->get($route);
            $response->assertOk()
                ->assertDontSee('href="#"', false)
                ->assertDontSee('🚀')
                ->assertDontSee('✨')
                ->assertDontSee('✅')
                ->assertDontSee('🎉');
        }
    }
}
