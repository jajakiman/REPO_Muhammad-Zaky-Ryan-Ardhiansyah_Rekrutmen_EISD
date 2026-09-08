<?php

namespace Tests\Feature\Admin;

use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusLocationManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Perpustakaan Pusat', 'location_type' => 'library', 'description' => 'Lantai dasar',
            'latitude' => -6.973, 'longitude' => 107.63, 'accessibility_status' => 'partially_accessible',
        ], $overrides);
    }

    public function test_admin_can_read_create_edit_update_and_deactivate_locations_with_manual_coordinates(): void
    {
        $admin = $this->admin();
        $area = CampusArea::factory()->create();
        $campus = $area->campus;
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'name' => 'Gedung Lama']);

        $this->actingAs($admin)->get(route('admin.campuses.areas.locations.index', [$campus, $area]))
            ->assertOk()
            ->assertSee('Gedung Lama')
            ->assertSee('Belum Dinilai')
            ->assertSee('Gedung')
            ->assertDontSee('>not_assessed<', false)
            ->assertDontSee('>building<', false);
        $this->actingAs($admin)->get(route('admin.campuses.areas.locations.create', [$campus, $area]))->assertOk()->assertSee('Latitude')->assertDontSee('Pilih dari peta');
        $this->actingAs($admin)->post(route('admin.campuses.areas.locations.store', [$campus, $area]), $this->validData())
            ->assertRedirect(route('admin.campuses.areas.locations.index', [$campus, $area]));
        $this->assertDatabaseHas('campus_locations', ['campus_area_id' => $area->id, 'name' => 'Perpustakaan Pusat', 'location_type' => 'library']);

        $this->actingAs($admin)->get(route('admin.campuses.areas.locations.edit', [$campus, $area, $location]))->assertOk()->assertSee('Gedung Lama');
        $this->actingAs($admin)->put(route('admin.campuses.areas.locations.update', [$campus, $area, $location]), $this->validData([
            'name' => 'Gedung Baru', 'location_type' => 'building', 'accessibility_status' => 'accessible', 'is_active' => '1',
        ]))->assertRedirect(route('admin.campuses.areas.locations.index', [$campus, $area]));
        $this->actingAs($admin)->patch(route('admin.campuses.areas.locations.deactivate', [$campus, $area, $location]))->assertRedirect();
        $this->assertDatabaseHas('campus_locations', ['id' => $location->id, 'name' => 'Gedung Baru', 'is_active' => false]);
    }

    public function test_location_validation_enforces_coordinates_exact_enums_and_unique_area_name(): void
    {
        $area = CampusArea::factory()->create();
        CampusLocation::factory()->create(['campus_area_id' => $area->id, 'name' => 'Duplikat']);

        $this->actingAs($this->admin())->post(route('admin.campuses.areas.locations.store', [$area->campus, $area]), $this->validData([
            'name' => 'Duplikat', 'location_type' => 'mall', 'latitude' => -91, 'longitude' => 181, 'accessibility_status' => 'unknown',
        ]))->assertSessionHasErrors(['name', 'location_type', 'latitude', 'longitude', 'accessibility_status']);

        $locationTypes = ['building', 'library', 'worship_place', 'green_space', 'parking', 'pedestrian_area', 'shuttle_stop'];
        $statuses = ['accessible', 'partially_accessible', 'inaccessible', 'not_assessed'];
        foreach ($locationTypes as $index => $type) {
            $this->actingAs($this->admin())->post(route('admin.campuses.areas.locations.store', [$area->campus, $area]), $this->validData([
                'name' => 'Lokasi '.$index, 'location_type' => $type, 'accessibility_status' => $statuses[$index % count($statuses)],
            ]))->assertSessionHasNoErrors();
        }
    }

    public function test_inactive_hierarchy_rejects_new_locations(): void
    {
        $area = CampusArea::factory()->create(['is_active' => false]);
        $this->actingAs($this->admin())->post(route('admin.campuses.areas.locations.store', [$area->campus, $area]), $this->validData())->assertSessionHasErrors('area');

        $area->update(['is_active' => true]);
        $area->campus->update(['is_active' => false]);
        $this->actingAs($this->admin())->post(route('admin.campuses.areas.locations.store', [$area->campus, $area]), $this->validData())->assertSessionHasErrors('area');
    }

    public function test_location_routes_enforce_campus_and_area_relationship_context(): void
    {
        $area = CampusArea::factory()->create();
        $foreignCampus = Campus::factory()->create();
        $foreignLocation = CampusLocation::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.campuses.areas.locations.index', [$foreignCampus, $area]))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.campuses.areas.locations.edit', [$area->campus, $area, $foreignLocation]))->assertNotFound();
        $this->actingAs($admin)->put(route('admin.campuses.areas.locations.update', [$area->campus, $area, $foreignLocation]), $this->validData(['is_active' => 1]))->assertNotFound();
        $this->actingAs($admin)->patch(route('admin.campuses.areas.locations.deactivate', [$area->campus, $area, $foreignLocation]))->assertNotFound();
    }

    public function test_non_admin_cannot_access_location_routes(): void
    {
        $area = CampusArea::factory()->create();
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $routes = [
            ['get', route('admin.campuses.areas.locations.index', [$area->campus, $area])], ['get', route('admin.campuses.areas.locations.create', [$area->campus, $area])],
            ['post', route('admin.campuses.areas.locations.store', [$area->campus, $area])], ['get', route('admin.campuses.areas.locations.edit', [$area->campus, $area, $location])],
            ['put', route('admin.campuses.areas.locations.update', [$area->campus, $area, $location])], ['patch', route('admin.campuses.areas.locations.deactivate', [$area->campus, $area, $location])],
        ];
        foreach ($routes as [$method, $uri]) {
            $this->actingAs(User::factory()->create(['role' => 'officer']))->{$method}($uri)->assertForbidden();
        }
    }
}
