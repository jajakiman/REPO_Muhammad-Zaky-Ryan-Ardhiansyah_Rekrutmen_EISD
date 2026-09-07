<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_role_routes(): void
    {
        foreach (['reporter.dashboard', 'officer.dashboard', 'admin.dashboard'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_each_role_can_access_its_own_route(): void
    {
        foreach (['reporter', 'officer', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get(route($role.'.dashboard'))->assertOk();
        }
    }

    public function test_authenticated_role_mismatches_return_forbidden(): void
    {
        foreach (['reporter', 'officer', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            foreach (array_diff(['reporter', 'officer', 'admin'], [$role]) as $otherRole) {
                $this->actingAs($user)->get(route($otherRole.'.dashboard'))->assertForbidden();
            }
        }
    }

    public function test_actor_dashboards_use_dedicated_sidebar_layout_without_public_landing_footer(): void
    {
        foreach (['reporter', 'officer', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get(route($role.'.dashboard'));

            $response->assertOk()
                ->assertSee('dashboard-sidebar', false)
                ->assertSee('sidebar-navigation', false)
                ->assertDontSee('site-footer', false)
                ->assertDontSee('FAQ</a>', false);
        }
    }

    public function test_mobile_dashboard_sidebar_has_accessible_controls_and_backdrop(): void
    {
        $user = User::factory()->create(['role' => 'reporter']);

        $response = $this->actingAs($user)->get(route('reporter.dashboard'));

        $response->assertOk()
            ->assertSee('id="sidebar-toggle-btn"', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('aria-controls="dashboard-sidebar"', false)
            ->assertSee('id="sidebar-backdrop"', false)
            ->assertSee('id="sidebar-close-btn"', false);
    }
}
