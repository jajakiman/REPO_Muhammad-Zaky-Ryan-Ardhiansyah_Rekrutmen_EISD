<?php

namespace Tests\Feature\Admin;

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

class AdminTableControlsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_toggle_status_in_both_directions_for_every_active_resource(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create(['is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => true]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'is_active' => true]);
        $feature = AccessibilityFeature::factory()->create(['is_active' => true]);
        $category = IssueCategory::factory()->create(['is_active' => true]);
        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
            'is_active' => true,
        ]);

        $resources = [
            [$campus, route('admin.campuses.status', $campus)],
            [$area, route('admin.campuses.areas.status', [$campus, $area])],
            [$location, route('admin.campuses.areas.locations.status', [$campus, $area, $location])],
            [$feature, route('admin.features.status', $feature)],
            [$category, route('admin.issue-categories.status', $category)],
            [$officer, route('admin.officers.status', $officer)],
        ];

        foreach ($resources as [$resource, $url]) {
            $this->actingAs($admin)->patch($url, ['is_active' => '0'])->assertRedirect();
            $this->assertFalse($resource->fresh()->is_active);
            $this->actingAs($admin)->patch($url, ['is_active' => '1'])->assertRedirect();
            $this->assertTrue($resource->fresh()->is_active);
        }
    }

    public function test_area_and_location_cannot_be_activated_under_an_inactive_parent(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create(['is_active' => false]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id, 'is_active' => false]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id, 'is_active' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.campuses.areas.status', [$campus, $area]), ['is_active' => '1'])
            ->assertSessionHasErrors('is_active');

        $campus->update(['is_active' => true]);
        $this->actingAs($admin)
            ->patch(route('admin.campuses.areas.locations.status', [$campus, $area, $location]), ['is_active' => '1'])
            ->assertSessionHasErrors('is_active');
    }

    public function test_status_endpoints_reject_invalid_values_and_non_admins(): void
    {
        $campus = Campus::factory()->create();

        $this->actingAs($this->admin())
            ->patch(route('admin.campuses.status', $campus), ['is_active' => 'invalid'])
            ->assertSessionHasErrors('is_active');

        $this->actingAs(User::factory()->create(['role' => 'reporter']))
            ->patch(route('admin.campuses.status', $campus), ['is_active' => '0'])
            ->assertForbidden();

        $this->assertTrue($campus->fresh()->is_active);
    }

    public function test_admin_tables_use_accessible_switches_and_button_actions(): void
    {
        $admin = $this->admin();
        $campus = Campus::factory()->create(['is_active' => true]);
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $category = IssueCategory::factory()->create();
        $assignment = LocationAccessibilityFeature::factory()->create([
            'campus_location_id' => $location->id,
            'accessibility_feature_id' => $feature->id,
        ]);
        $officer = User::factory()->create([
            'role' => 'officer',
            'campus_id' => $campus->id,
            'campus_area_id' => $area->id,
        ]);
        AccessibilityReport::factory()->create([
            'reporter_id' => $admin->id,
            'officer_id' => $officer->id,
            'location_accessibility_feature_id' => $assignment->id,
            'issue_category_id' => $category->id,
        ]);

        $switchRoutes = [
            route('admin.campuses.index'),
            route('admin.campuses.areas.index', $campus),
            route('admin.campuses.areas.locations.index', [$campus, $area]),
            route('admin.features.index'),
            route('admin.issue-categories.index'),
            route('admin.officers.index'),
        ];

        foreach ($switchRoutes as $url) {
            $this->actingAs($admin)->get($url)
                ->assertOk()
                ->assertSee('data-status-switch', false)
                ->assertSee('peer-checked:bg-emerald-700', false)
                ->assertSee('class="button button-secondary button-sm"', false);
        }

        foreach ([route('admin.locations.features.index', $location), route('admin.reports.index')] as $url) {
            $this->actingAs($admin)->get($url)
                ->assertOk()
                ->assertSee('class="button button-secondary button-sm"', false);
        }
    }
}
