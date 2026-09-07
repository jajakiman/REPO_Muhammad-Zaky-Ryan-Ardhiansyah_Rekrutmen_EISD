<?php

namespace Tests\Feature\Admin;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CampusManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_read_create_edit_update_and_deactivate_campuses(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create(['name' => 'Kampus Lama']);

        $this->actingAs($admin)->get(route('admin.campuses.index'))->assertOk()->assertSee('Kampus Lama');
        $this->actingAs($admin)->get(route('admin.campuses.create'))->assertOk()->assertSee('Nama kampus');
        $this->actingAs($admin)->post(route('admin.campuses.store'), [
            'name' => 'Kampus Baru', 'address' => 'Jalan Utama',
        ])->assertRedirect(route('admin.campuses.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('success_modal', true)
            ->assertSessionMissing('success_modal_auto_close');
        $this->assertDatabaseHas('campuses', ['name' => 'Kampus Baru', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.campuses.edit', $campus))->assertOk()->assertSee('Kampus Lama');
        $this->actingAs($admin)->put(route('admin.campuses.update', $campus), [
            'name' => 'Kampus Diperbarui', 'address' => '', 'is_active' => '1',
        ])->assertRedirect(route('admin.campuses.index'));
        $this->assertDatabaseHas('campuses', ['id' => $campus->id, 'name' => 'Kampus Diperbarui', 'address' => null]);

        $this->actingAs($admin)->patch(route('admin.campuses.deactivate', $campus))->assertRedirect(route('admin.campuses.index'));
        $this->assertDatabaseHas('campuses', ['id' => $campus->id, 'is_active' => false]);
        $this->assertTrue(Route::has('admin.campuses.deactivate'));
    }

    public function test_campus_management_does_not_expose_or_accept_coordinates(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create([
            'latitude' => -6.9000000,
            'longitude' => 107.6000000,
        ]);

        $this->actingAs($admin)->get(route('admin.campuses.index'))
            ->assertOk()
            ->assertDontSee('Koordinat');

        foreach ([route('admin.campuses.create'), route('admin.campuses.edit', $campus)] as $url) {
            $this->actingAs($admin)->get($url)
                ->assertOk()
                ->assertDontSee('name="latitude"', false)
                ->assertDontSee('name="longitude"', false);
        }

        $this->actingAs($admin)->post(route('admin.campuses.store'), [
            'name' => 'Kampus Tanpa Koordinat',
            'latitude' => -6.2000000,
            'longitude' => 106.8000000,
        ])->assertRedirect(route('admin.campuses.index'));

        $this->assertDatabaseHas('campuses', [
            'name' => 'Kampus Tanpa Koordinat',
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    public function test_campus_validation_rejects_duplicate_names_with_old_input(): void
    {
        $admin = $this->admin();
        Campus::factory()->create(['name' => 'Kampus Sama']);

        $this->actingAs($admin)->from(route('admin.campuses.create'))->post(route('admin.campuses.store'), [
            'name' => 'Kampus Sama',
        ])->assertRedirect(route('admin.campuses.create'))
            ->assertSessionHasErrors(['name'])
            ->assertSessionHasInput('name', 'Kampus Sama');
    }

    public function test_campus_update_ignores_its_own_name_for_uniqueness(): void
    {
        $campus = Campus::factory()->create(['name' => 'Tetap Unik']);

        $this->actingAs($this->admin())->put(route('admin.campuses.update', $campus), [
            'name' => 'Tetap Unik', 'is_active' => '1',
        ])->assertRedirect(route('admin.campuses.index'))->assertSessionHasNoErrors();
    }

    public function test_only_admins_can_access_every_campus_endpoint(): void
    {
        $campus = Campus::factory()->create();
        $requests = [
            ['get', route('admin.campuses.index'), []], ['get', route('admin.campuses.create'), []],
            ['post', route('admin.campuses.store'), []], ['get', route('admin.campuses.edit', $campus), []],
            ['put', route('admin.campuses.update', $campus), []], ['patch', route('admin.campuses.deactivate', $campus), []],
        ];

        foreach ($requests as [$method, $uri, $data]) {
            auth()->logout();
            $this->{$method}($uri, $data)->assertRedirect(route('login'));
            foreach (['reporter', 'officer'] as $role) {
                $this->actingAs(User::factory()->create(['role' => $role]))->{$method}($uri, $data)->assertForbidden();
            }
        }
    }
}
