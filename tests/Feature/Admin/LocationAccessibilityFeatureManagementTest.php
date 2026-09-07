<?php

namespace Tests\Feature\Admin;

use App\Models\AccessibilityFeature;
use App\Models\CampusLocation;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocationAccessibilityFeatureManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function data(AccessibilityFeature $feature, array $overrides = []): array
    {
        return array_merge([
            'accessibility_feature_id' => $feature->id,
            'availability_status' => 'available',
            'condition' => 'good',
            'notes' => 'Diperiksa petugas kampus',
            'last_checked_at' => '2026-09-07T10:30',
        ], $overrides);
    }

    public function test_admin_can_read_create_edit_and_update_location_facility_details(): void
    {
        $admin = $this->admin();
        $location = CampusLocation::factory()->create(['name' => 'Perpustakaan']);
        $ramp = AccessibilityFeature::factory()->create(['name' => 'Ramp']);

        $this->actingAs($admin)->get(route('admin.locations.features.index', $location))->assertOk()->assertSee('Belum ada fasilitas');
        $this->actingAs($admin)->get(route('admin.locations.features.create', $location))->assertOk()->assertSee('Ramp');
        $this->actingAs($admin)->post(route('admin.locations.features.store', $location), $this->data($ramp))
            ->assertRedirect(route('admin.locations.features.index', $location));
        $pivot = LocationAccessibilityFeature::firstOrFail();
        $this->assertDatabaseHas('location_accessibility_features', ['campus_location_id' => $location->id, 'accessibility_feature_id' => $ramp->id, 'condition' => 'good']);

        $this->actingAs($admin)->get(route('admin.locations.features.edit', [$location, $pivot]))->assertOk()->assertSee('Ramp');
        $this->actingAs($admin)->put(route('admin.locations.features.update', [$location, $pivot]), [
            'availability_status' => 'unavailable', 'condition' => 'blocked', 'notes' => 'Akses tertutup proyek', 'last_checked_at' => '2026-09-08T14:15',
        ])->assertRedirect(route('admin.locations.features.index', $location));
        $this->assertDatabaseHas('location_accessibility_features', [
            'id' => $pivot->id, 'availability_status' => 'unavailable', 'condition' => 'blocked', 'notes' => 'Akses tertutup proyek',
        ]);
        $this->assertSame('2026-09-08 14:15:00', $pivot->fresh()->last_checked_at->format('Y-m-d H:i:s'));
        $this->assertFalse(Route::has('admin.locations.features.destroy'));
    }

    public function test_assignment_rejects_duplicate_pairs_and_inactive_facilities(): void
    {
        $location = CampusLocation::factory()->create();
        $feature = AccessibilityFeature::factory()->create();
        LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id, 'accessibility_feature_id' => $feature->id]);

        $this->actingAs($this->admin())->post(route('admin.locations.features.store', $location), $this->data($feature))->assertSessionHasErrors('accessibility_feature_id');
        $inactive = AccessibilityFeature::factory()->create(['is_active' => false]);
        $this->actingAs($this->admin())->post(route('admin.locations.features.store', $location), $this->data($inactive))->assertSessionHasErrors('accessibility_feature_id');
        $this->actingAs($this->admin())->get(route('admin.locations.features.create', $location))->assertDontSee($inactive->name);
    }

    public function test_pivot_validation_enforces_exact_enums_and_allows_nullable_metadata(): void
    {
        $location = CampusLocation::factory()->create();
        $feature = AccessibilityFeature::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.locations.features.store', $location), $this->data($feature, [
            'availability_status' => 'unknown', 'condition' => 'missing', 'last_checked_at' => 'not-a-date',
        ]))->assertSessionHasErrors(['availability_status', 'condition', 'last_checked_at']);

        foreach ([['available', 'needs_repair'], ['unavailable', 'broken']] as $index => [$availability, $condition]) {
            $current = AccessibilityFeature::factory()->create();
            $this->actingAs($this->admin())->post(route('admin.locations.features.store', $location), $this->data($current, [
                'availability_status' => $availability, 'condition' => $condition, 'notes' => null, 'last_checked_at' => null,
            ]))->assertSessionHasNoErrors();
            $this->assertDatabaseHas('location_accessibility_features', ['accessibility_feature_id' => $current->id, 'availability_status' => $availability, 'condition' => $condition, 'notes' => null, 'last_checked_at' => null]);
        }
    }

    public function test_pivot_routes_enforce_location_relationship_context(): void
    {
        $location = CampusLocation::factory()->create();
        $foreignPivot = LocationAccessibilityFeature::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.locations.features.edit', [$location, $foreignPivot]))->assertNotFound();
        $this->actingAs($admin)->put(route('admin.locations.features.update', [$location, $foreignPivot]), [
            'availability_status' => 'available', 'condition' => 'good',
        ])->assertNotFound();
    }

    public function test_non_admins_cannot_access_location_facility_endpoints(): void
    {
        $location = CampusLocation::factory()->create();
        $pivot = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id]);
        $routes = [
            ['get', route('admin.locations.features.index', $location)], ['get', route('admin.locations.features.create', $location)],
            ['post', route('admin.locations.features.store', $location)], ['get', route('admin.locations.features.edit', [$location, $pivot])],
            ['put', route('admin.locations.features.update', [$location, $pivot])],
        ];
        foreach ($routes as [$method, $uri]) {
            $this->actingAs(User::factory()->create(['role' => 'officer']))->{$method}($uri)->assertForbidden();
        }
    }
}
