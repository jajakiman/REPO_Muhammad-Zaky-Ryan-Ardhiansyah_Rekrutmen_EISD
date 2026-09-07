<?php

namespace Tests\Feature;

use App\Models\AccessibilityFeature;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_map_and_sees_only_active_locations(): void
    {
        $campus = Campus::factory()->create(['name' => 'Universitas Pendidikan Indonesia']);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'FPMIPA']);
        $activeLocation = CampusLocation::factory()->create([
            'campus_area_id' => $area->id,
            'name' => 'Gedung JICA',
            'accessibility_status' => 'accessible',
            'latitude' => -6.8601234,
            'longitude' => 107.5891234,
            'is_active' => true,
        ]);
        $inactiveLocation = CampusLocation::factory()->create([
            'campus_area_id' => $area->id,
            'name' => 'Gedung Tertutup',
            'is_active' => false,
        ]);

        $response = $this->get(route('map.index'));

        $response->assertOk()
            ->assertSee('Peta Aksesibilitas Kampus')
            ->assertSee('Gedung JICA')
            ->assertDontSee('Gedung Tertutup')
            ->assertSee('OpenStreetMap')
            ->assertSee('Daftar Lokasi Kampus')
            ->assertSee('accessible');
    }

    public function test_public_map_filters_use_interactive_native_select_components(): void
    {
        $this->get(route('map.index'))
            ->assertOk()
            ->assertSee('select-shell', false)
            ->assertSee('select-chevron', false)
            ->assertSee('name="campus_id"', false)
            ->assertSee('name="location_type"', false)
            ->assertSee('name="accessibility_status"', false);
    }

    public function test_map_filters_by_name_campus_type_and_accessibility_status(): void
    {
        $campus1 = Campus::factory()->create(['name' => 'Telkom University']);
        $area1 = CampusArea::factory()->create(['campus_id' => $campus1->id]);
        $loc1 = CampusLocation::factory()->create([
            'campus_area_id' => $area1->id,
            'name' => 'Perpustakaan Telkom',
            'location_type' => 'library',
            'accessibility_status' => 'accessible',
        ]);

        $campus2 = Campus::factory()->create(['name' => 'Universitas Teknologi Bandung']);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus2->id]);
        $loc2 = CampusLocation::factory()->create([
            'campus_area_id' => $area2->id,
            'name' => 'Masjid Kampus',
            'location_type' => 'worship_place',
            'accessibility_status' => 'partially_accessible',
        ]);

        // Search by keyword
        $this->get(route('map.index', ['q' => 'Perpustakaan']))
            ->assertOk()
            ->assertSee('Perpustakaan Telkom')
            ->assertDontSee('Masjid Kampus');

        // Filter by campus
        $this->get(route('map.index', ['campus_id' => $campus2->id]))
            ->assertOk()
            ->assertSee('Masjid Kampus')
            ->assertDontSee('Perpustakaan Telkom');

        // Filter by location type
        $this->get(route('map.index', ['location_type' => 'library']))
            ->assertOk()
            ->assertSee('Perpustakaan Telkom')
            ->assertDontSee('Masjid Kampus');

        // Filter by accessibility status
        $this->get(route('map.index', ['accessibility_status' => 'partially_accessible']))
            ->assertOk()
            ->assertSee('Masjid Kampus')
            ->assertDontSee('Perpustakaan Telkom');

        // No matches shows empty state
        $this->get(route('map.index', ['q' => 'TidakAdaLokasiSepertiIni']))
            ->assertOk()
            ->assertSee('Tidak ada lokasi yang sesuai dengan filter pencarian.');
    }

    public function test_logged_in_reporter_with_campus_defaults_to_their_campus(): void
    {
        $campus1 = Campus::factory()->create(['name' => 'Telkom University']);
        $area1 = CampusArea::factory()->create(['campus_id' => $campus1->id]);
        CampusLocation::factory()->create(['campus_area_id' => $area1->id, 'name' => 'Gedung Tokong Nanas']);

        $campus2 = Campus::factory()->create(['name' => 'UPI']);
        $area2 = CampusArea::factory()->create(['campus_id' => $campus2->id]);
        CampusLocation::factory()->create(['campus_area_id' => $area2->id, 'name' => 'Gedung Isola']);

        $reporter = User::factory()->create([
            'role' => 'reporter',
            'campus_id' => $campus1->id,
            'affiliation_type' => 'student',
        ]);

        $response = $this->actingAs($reporter)->get(route('map.index'));

        $response->assertOk()
            ->assertSee('Gedung Tokong Nanas');
    }

    public function test_guest_can_view_location_detail_and_sees_facilities(): void
    {
        $campus = Campus::factory()->create(['name' => 'Telkom University']);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'name' => 'Area Utama']);
        $location = CampusLocation::factory()->create([
            'campus_area_id' => $area->id,
            'name' => 'Perpustakaan Pusat',
            'location_type' => 'library',
            'description' => 'Gedung perpustakaan 3 lantai.',
            'accessibility_status' => 'accessible',
        ]);

        $feature = AccessibilityFeature::factory()->create(['name' => 'Ramp']);
        LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
            'availability_status' => 'available',
            'condition' => 'good',
            'notes' => 'Ramp di sisi barat pintu masuk.',
        ]);

        $response = $this->get(route('locations.show', $location));

        $response->assertOk()
            ->assertSee('Perpustakaan Pusat')
            ->assertSee('Telkom University')
            ->assertSee('Area Utama')
            ->assertSee('Gedung perpustakaan 3 lantai.')
            ->assertSee('Ramp')
            ->assertSee('Tersedia')
            ->assertSee('Baik')
            ->assertSee('Ramp di sisi barat pintu masuk.')
            ->assertSee('Laporkan Masalah');
    }

    public function test_viewing_inactive_location_or_inactive_campus_returns_404(): void
    {
        $campus = Campus::factory()->create(['is_active' => false]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);

        $this->get(route('locations.show', $location))->assertNotFound();
    }

    public function test_guest_clicking_report_is_directed_to_login(): void
    {
        $location = CampusLocation::factory()->create();
        $feature = AccessibilityFeature::factory()->create();
        $pivot = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);

        $response = $this->get(route('locations.show', $location));

        $response->assertOk()
            ->assertSee(route('login'));
    }
}
