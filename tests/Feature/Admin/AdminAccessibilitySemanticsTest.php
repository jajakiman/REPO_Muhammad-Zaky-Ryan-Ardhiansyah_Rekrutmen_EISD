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
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AdminAccessibilitySemanticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_table_cell_has_mobile_context(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $category = IssueCategory::factory()->create();
        $laf = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id, 'accessibility_feature_id' => $feature->id]);
        $officer = User::factory()->create(['role' => 'officer', 'campus_id' => $campus->id, 'campus_area_id' => $area->id]);
        AccessibilityReport::factory()->create([
            'location_accessibility_feature_id' => $laf->id,
            'issue_category_id' => $category->id,
            'reporter_id' => $admin->id,
        ]);

        $routes = [
            route('admin.campuses.index'), route('admin.campuses.areas.index', $campus),
            route('admin.campuses.areas.locations.index', [$campus, $area]), route('admin.features.index'),
            route('admin.issue-categories.index'), route('admin.locations.features.index', $location),
            route('admin.officers.index'), route('admin.reports.index'),
        ];

        foreach ($routes as $route) {
            $xpath = $this->xpath($this->actingAs($admin)->get($route)->assertOk()->getContent());
            $cells = $xpath->query('//tbody/tr/*[self::th or self::td]');
            $this->assertGreaterThan(0, $cells->length, $route);
            foreach ($cells as $cell) {
                $this->assertNotSame('', $cell->getAttribute('data-label'), $route);
            }
            $this->assertSame(1, $xpath->query('//tbody/tr/td[@data-label="Aksi"]')->length, $route);
        }
    }

    public function test_mobile_table_css_displays_labels_and_prevents_content_overflow(): void
    {
        $css = file_get_contents(public_path('css/app.css'));

        $this->assertStringContainsString('content: attr(data-label)', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere', $css);
        $this->assertStringContainsString('minmax(0, 1fr)', $css);
        $this->assertStringContainsString('white-space: normal', $css);
    }

    public function test_admin_form_errors_are_programmatically_associated_with_invalid_controls(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $campus = Campus::factory()->create();
        $area = CampusArea::factory()->create(['campus_id' => $campus->id]);
        $location = CampusLocation::factory()->create(['campus_area_id' => $area->id]);
        $feature = AccessibilityFeature::factory()->create();
        $category = IssueCategory::factory()->create();
        $assignment = LocationAccessibilityFeature::factory()->create(['campus_location_id' => $location->id, 'accessibility_feature_id' => $feature->id]);
        $officer = User::factory()->create(['role' => 'officer', 'campus_id' => $campus->id, 'campus_area_id' => $area->id]);
        AccessibilityFeature::factory()->create();

        $forms = [
            [route('admin.campuses.edit', $campus), ['name', 'address', 'latitude', 'longitude', 'is_active']],
            [route('admin.campuses.areas.edit', [$campus, $area]), ['name', 'is_active']],
            [route('admin.campuses.areas.locations.edit', [$campus, $area, $location]), ['name', 'location_type', 'description', 'latitude', 'longitude', 'accessibility_status', 'is_active']],
            [route('admin.features.edit', $feature), ['name', 'is_active']],
            [route('admin.issue-categories.edit', $category), ['name', 'is_active']],
            [route('admin.locations.features.create', $location), ['accessibility_feature_id', 'availability_status', 'condition', 'notes', 'last_checked_at']],
            [route('admin.locations.features.edit', [$location, $assignment]), ['availability_status', 'condition', 'notes', 'last_checked_at']],
            [route('admin.officers.create'), ['name', 'email', 'password', 'campus_area_id']],
            [route('admin.officers.edit', $officer), ['name', 'campus_area_id', 'is_active']],
        ];

        foreach ($forms as [$route, $fields]) {
            $errors = array_fill_keys($fields, 'Input tidak valid.');
            $errorBag = (new ViewErrorBag)->put('default', new MessageBag($errors));
            $html = $this->actingAs($admin)->withSession(['errors' => $errorBag])->get($route)->assertOk()->getContent();
            $xpath = $this->xpath($html);
            foreach ($fields as $field) {
                $control = $xpath->query("//*[self::input or self::select or self::textarea][@name='$field']")->item(0);
                $this->assertNotNull($control, "$route: $field");
                $this->assertSame('true', $control->getAttribute('aria-invalid'), "$route: $field");
                $errorId = $control->getAttribute('aria-describedby');
                $this->assertNotSame('', $errorId, "$route: $field");
                $this->assertSame(1, $xpath->query("//*[@id='$errorId']")->length, "$route: $field");
            }
        }

        $edit = $this->xpath($this->actingAs($admin)->get(route('admin.locations.features.edit', [$location, $assignment]))->getContent());
        $this->assertSame(1, $edit->query('//label[@for="feature_name"]')->length);
        $this->assertSame(1, $edit->query('//input[@id="feature_name" and @readonly]')->length);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html);

        return new DOMXPath($document);
    }
}
