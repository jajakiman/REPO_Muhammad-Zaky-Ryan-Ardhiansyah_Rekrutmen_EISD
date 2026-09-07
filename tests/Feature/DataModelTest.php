<?php

namespace Tests\Feature;

use App\Models\AccessibilityFeature;
use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_reports_belong_to_reporters_officers_categories_and_location_features(): void
    {
        $reporter = User::factory()->create();
        $officer = User::factory()->create(['role' => 'officer']);
        $category = IssueCategory::factory()->create();
        $locationFeature = LocationAccessibilityFeature::factory()->create();
        $report = AccessibilityReport::factory()
            ->for($reporter, 'reporter')
            ->for($officer, 'officer')
            ->for($category)
            ->for($locationFeature)
            ->create(['priority' => 'high', 'verified_at' => '2026-09-07 10:00:00']);

        $this->assertTrue($report->reporter->is($reporter));
        $this->assertTrue($report->officer->is($officer));
        $this->assertTrue($report->issueCategory->is($category));
        $this->assertTrue($report->locationAccessibilityFeature->is($locationFeature));
        $this->assertTrue($reporter->reports->contains($report));
        $this->assertTrue($officer->handledReports->contains($report));
        $this->assertTrue($category->reports->contains($report));
        $this->assertTrue($locationFeature->reports->contains($report));
        $this->assertNotNull($report->verified_at);
    }

    public function test_database_seeder_is_idempotent_and_contains_listed_master_and_demo_values(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame([
            'Telkom University Bandung',
            'Universitas Pendidikan Indonesia',
            'Universitas Teknologi Bandung',
        ], Campus::orderBy('id')->pluck('name')->all());
        $this->assertSame([
            'Ramp',
            'Lift',
            'Guiding Block',
            'Toilet Aksesibel',
            'Handrail',
            'Parkir Disabilitas',
            'Pintu Aksesibel',
        ], AccessibilityFeature::orderBy('id')->pluck('name')->all());
        $this->assertSame([
            'Fasilitas Rusak',
            'Akses Terhalang',
            'Tidak Dapat Digunakan',
            'Permukaan Tidak Aman',
            'Penerangan Tidak Memadai',
            'Signage Tidak Jelas',
        ], IssueCategory::orderBy('id')->pluck('name')->all());

        $this->assertSame(3, DB::table('campus_areas')->count());
        $this->assertSame(3, DB::table('campus_locations')->count());
        $this->assertSame(9, DB::table('location_accessibility_features')->count());
        $this->assertSame(4, DB::table('users')->count());
        $this->assertSame(0, DB::table('accessibility_reports')->count());
    }
}
