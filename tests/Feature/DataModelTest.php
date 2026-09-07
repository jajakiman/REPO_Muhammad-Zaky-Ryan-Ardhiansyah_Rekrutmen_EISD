<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\User;
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
}
