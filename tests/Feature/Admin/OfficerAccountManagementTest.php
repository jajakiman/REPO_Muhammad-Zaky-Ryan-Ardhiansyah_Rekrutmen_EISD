<?php

namespace Tests\Feature\Admin;

use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfficerAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_access_officer_management_routes(): void
    {
        $officer = User::factory()->create(['role' => 'officer']);

        $endpoints = [
            ['get', route('admin.officers.index')],
            ['get', route('admin.officers.create')],
            ['post', route('admin.officers.store')],
            ['get', route('admin.officers.edit', $officer)],
            ['put', route('admin.officers.update', $officer)],
            ['patch', route('admin.officers.deactivate', $officer)],
        ];

        foreach ($endpoints as [$method, $uri]) {
            $this->actingAs(User::factory()->create(['role' => 'reporter']))->{$method}($uri)->assertForbidden();
            auth()->logout();
            $this->actingAs($officer)->{$method}($uri)->assertForbidden();
            auth()->logout();
            $this->{$method}($uri)->assertRedirect(route('login'));
        }
    }

    public function test_admin_can_view_officers_list(): void
    {
        $campus = Campus::factory()->create(['name' => 'Telkom University']);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Gedung Kuliah']);
        $officer = User::factory()->create([
            'role' => 'officer',
            'name' => 'Petugas Satu',
            'email' => 'petugas1@kampus.id',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())->get(route('admin.officers.index'));

        $response->assertOk()
            ->assertSee('Petugas Satu')
            ->assertSee('petugas1@kampus.id')
            ->assertSee('Telkom University')
            ->assertSee('Area Gedung Kuliah')
            ->assertSee('Aktif');
    }

    public function test_admin_can_create_an_officer_with_automatic_officer_role(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);

        $payload = [
            'name' => 'Petugas Baru',
            'email' => 'baru@kampus.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'campus_area_id' => $area->id,
        ];

        $response = $this->actingAs($this->admin())->post(route('admin.officers.store'), $payload);

        $response->assertRedirect(route('admin.officers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Petugas Baru',
            'email' => 'baru@kampus.id',
            'role' => 'officer',
            'campus_area_id' => $area->id,
            'campus_id' => $campus->id,
            'is_active' => true,
        ]);

        $created = User::where('email', 'baru@kampus.id')->firstOrFail();
        $this->assertTrue(Hash::check('Password123!', $created->password));
    }

    public function test_officer_creation_validation_requires_unique_email_and_active_area(): void
    {
        $campus = Campus::factory()->create(['is_active' => false]);
        $inactiveArea = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => true]);
        $existing = User::factory()->create(['email' => 'terdaftar@kampus.id']);

        $response = $this->actingAs($this->admin())->post(route('admin.officers.store'), [
            'name' => '',
            'email' => 'terdaftar@kampus.id',
            'password' => 'pendek',
            'password_confirmation' => 'beda',
            'campus_area_id' => $inactiveArea->id,
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'campus_area_id']);
    }

    public function test_admin_can_reassign_officer_area_and_update_profile(): void
    {
        $campus1 = Campus::factory()->create();
        $area1 = CampusArea::factory()->create(['campus_id' => $campus1->id]);

        $campus2 = Campus::factory()->create();
        $area2 = CampusArea::factory()->create(['campus_id' => $campus2->id]);

        $officer = User::factory()->create([
            'role' => 'officer',
            'name' => 'Nama Awal',
            'campus_id' => $campus1->id,
            'campus_area_id' => $area1->id,
        ]);

        $response = $this->actingAs($this->admin())->put(route('admin.officers.update', $officer), [
            'name' => 'Nama Baru',
            'campus_area_id' => $area2->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.officers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $officer->id,
            'name' => 'Nama Baru',
            'campus_area_id' => $area2->id,
            'campus_id' => $campus2->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_deactivate_an_officer(): void
    {
        $officer = User::factory()->create(['role' => 'officer', 'is_active' => true]);

        $response = $this->actingAs($this->admin())->patch(route('admin.officers.deactivate', $officer));

        $response->assertRedirect(route('admin.officers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $officer->id,
            'is_active' => false,
        ]);
    }
}
