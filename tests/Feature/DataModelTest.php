<?php

namespace Tests\Feature;

use App\Models\AccessibilityFeature;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_hierarchy_and_user_affiliations_are_related(): void
    {
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->for($campus)->create();
        $location = CampusLocation::factory()->for($area)->create();
        $reporter = User::factory()->for($campus)->create();
        $officer = User::factory()->for($area)->create(['role' => 'officer']);

        $this->assertTrue($campus->areas->contains($area));
        $this->assertTrue($area->locations->contains($location));
        $this->assertTrue($campus->users->contains($reporter));
        $this->assertTrue($area->officers->contains($officer));
        $this->assertTrue($location->campusArea->is($area));
        $this->assertTrue($officer->campusArea->is($area));
    }

    public function test_active_scopes_exclude_inactive_campus_master_data(): void
    {
        Campus::factory()->create(['is_active' => false]);
        $campus = Campus::factory()->create();
        CampusArea::factory()->for($campus)->create(['is_active' => false]);
        $area = CampusArea::factory()->for($campus)->create();
        CampusLocation::factory()->for($area)->create(['is_active' => false]);
        $location = CampusLocation::factory()->for($area)->create();

        $this->assertEquals([$campus->id], Campus::active()->pluck('id')->all());
        $this->assertEquals([$area->id], CampusArea::active()->pluck('id')->all());
        $this->assertEquals([$location->id], CampusLocation::active()->pluck('id')->all());
    }

    public function test_location_features_are_related_through_a_unique_detailed_record(): void
    {
        $location = CampusLocation::factory()->create();
        $feature = AccessibilityFeature::factory()->create();
        $locationFeature = LocationAccessibilityFeature::factory()
            ->for($location)
            ->for($feature)
            ->create(['last_checked_at' => '2026-09-07 10:00:00']);

        $this->assertTrue($location->locationAccessibilityFeatures->contains($locationFeature));
        $this->assertTrue($feature->locationAccessibilityFeatures->contains($locationFeature));
        $this->assertTrue($location->accessibilityFeatures->contains($feature));
        $this->assertTrue($locationFeature->campusLocation->is($location));
        $this->assertTrue($locationFeature->accessibilityFeature->is($feature));
        $this->assertNotNull($locationFeature->last_checked_at);

        $this->expectException(UniqueConstraintViolationException::class);
        LocationAccessibilityFeature::factory()->for($location)->for($feature)->create();
    }

    public function test_active_scopes_exclude_inactive_features_and_categories(): void
    {
        AccessibilityFeature::factory()->create(['is_active' => false]);
        $feature = AccessibilityFeature::factory()->create();
        IssueCategory::factory()->create(['is_active' => false]);
        $category = IssueCategory::factory()->create();

        $this->assertEquals([$feature->id], AccessibilityFeature::active()->pluck('id')->all());
        $this->assertEquals([$category->id], IssueCategory::active()->pluck('id')->all());
    }
}
