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
}
