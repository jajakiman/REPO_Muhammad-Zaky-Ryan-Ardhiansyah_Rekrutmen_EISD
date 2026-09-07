<?php

namespace Tests\Feature\Admin;

use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusAreaManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_read_create_edit_update_and_deactivate_areas(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create(['name' => 'Kampus Utama']);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Lama']);

        $this->actingAs($admin)->get(route('admin.campuses.areas.index', $campus))->assertOk()->assertSee('Area Lama');
        $this->actingAs($admin)->get(route('admin.campuses.areas.create', $campus))->assertOk()->assertSee('Nama area');
        $this->actingAs($admin)->post(route('admin.campuses.areas.store', $campus), ['name' => 'Area Baru'])
            ->assertRedirect(route('admin.campuses.areas.index', $campus));
        $this->assertDatabaseHas('campus_areas', ['campus_id' => $campus->id, 'name' => 'Area Baru', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.campuses.areas.edit', [$campus, $area]))->assertOk()->assertSee('Area Lama');
        $this->actingAs($admin)->put(route('admin.campuses.areas.update', [$campus, $area]), ['name' => 'Area Baru Nama', 'is_active' => '1'])
            ->assertRedirect(route('admin.campuses.areas.index', $campus));
        $this->actingAs($admin)->patch(route('admin.campuses.areas.deactivate', [$campus, $area]))
            ->assertRedirect(route('admin.campuses.areas.index', $campus));
        $this->assertDatabaseHas('campus_areas', ['id' => $area->id, 'name' => 'Area Baru Nama', 'is_active' => false]);
    }

    public function test_area_names_are_unique_per_campus_and_an_inactive_campus_rejects_new_areas(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create();
        CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Rektorat']);
        $other = Campus::factory()->create();

        $this->actingAs($admin)->post(route('admin.campuses.areas.store', $campus), ['name' => 'Rektorat'])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post(route('admin.campuses.areas.store', $other), ['name' => 'Rektorat'])->assertSessionHasNoErrors();
        $campus->update(['is_active' => false]);
        $this->actingAs($admin)->post(route('admin.campuses.areas.store', $campus), ['name' => 'Area Ditolak'])->assertSessionHasErrors('campus');
    }

    public function test_nested_area_endpoints_reject_an_area_from_another_campus(): void
    {
        $campus = Campus::factory()->create();
        $foreignArea = CampusArea::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.campuses.areas.edit', [$campus, $foreignArea]))->assertNotFound();
        $this->actingAs($admin)->put(route('admin.campuses.areas.update', [$campus, $foreignArea]), ['name' => 'X', 'is_active' => 1])->assertNotFound();
        $this->actingAs($admin)->patch(route('admin.campuses.areas.deactivate', [$campus, $foreignArea]))->assertNotFound();
    }

    public function test_only_admins_can_access_area_routes(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $uris = [
            ['get', route('admin.campuses.areas.index', $campus)], ['get', route('admin.campuses.areas.create', $campus)],
            ['post', route('admin.campuses.areas.store', $campus)], ['get', route('admin.campuses.areas.edit', [$campus, $area])],
            ['put', route('admin.campuses.areas.update', [$campus, $area])], ['patch', route('admin.campuses.areas.deactivate', [$campus, $area])],
        ];

        foreach ($uris as [$method, $uri]) {
            auth()->logout();
            $this->{$method}($uri)->assertRedirect(route('login'));
            $this->actingAs(User::factory()->create())->{$method}($uri)->assertForbidden();
        }
    }
}
