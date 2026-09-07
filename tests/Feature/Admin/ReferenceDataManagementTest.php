<?php

namespace Tests\Feature\Admin;

use App\Models\AccessibilityFeature;
use App\Models\IssueCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReferenceDataManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_manage_and_deactivate_accessibility_features(): void
    {
        $admin = $this->admin();
        $feature = AccessibilityFeature::factory()->create(['name' => 'Ramp Lama']);

        $this->actingAs($admin)->get(route('admin.features.index'))->assertOk()->assertSee('Ramp Lama');
        $this->actingAs($admin)->get(route('admin.features.create'))->assertOk()->assertSee('Nama fasilitas');
        $this->actingAs($admin)->post(route('admin.features.store'), ['name' => 'Lift Baru'])->assertRedirect(route('admin.features.index'));
        $this->actingAs($admin)->get(route('admin.features.edit', $feature))->assertOk()->assertSee('Ramp Lama');
        $this->actingAs($admin)->put(route('admin.features.update', $feature), ['name' => 'Ramp Utama', 'is_active' => 1])->assertRedirect(route('admin.features.index'));
        $this->actingAs($admin)->patch(route('admin.features.deactivate', $feature))->assertRedirect(route('admin.features.index'));

        $this->assertDatabaseHas('accessibility_features', ['id' => $feature->id, 'name' => 'Ramp Utama', 'is_active' => false]);
        $this->assertDatabaseHas('accessibility_features', ['name' => 'Lift Baru', 'is_active' => true]);
        $this->assertFalse(Route::has('admin.features.destroy'));
    }

    public function test_admin_can_manage_and_deactivate_issue_categories(): void
    {
        $admin = $this->admin();
        $category = IssueCategory::factory()->create(['name' => 'Rusak Lama']);

        $this->actingAs($admin)->get(route('admin.issue-categories.index'))->assertOk()->assertSee('Rusak Lama');
        $this->actingAs($admin)->get(route('admin.issue-categories.create'))->assertOk()->assertSee('Nama kategori');
        $this->actingAs($admin)->post(route('admin.issue-categories.store'), ['name' => 'Akses Terhalang'])->assertRedirect(route('admin.issue-categories.index'));
        $this->actingAs($admin)->get(route('admin.issue-categories.edit', $category))->assertOk()->assertSee('Rusak Lama');
        $this->actingAs($admin)->put(route('admin.issue-categories.update', $category), ['name' => 'Fasilitas Rusak', 'is_active' => 1])->assertRedirect(route('admin.issue-categories.index'));
        $this->actingAs($admin)->patch(route('admin.issue-categories.deactivate', $category))->assertRedirect(route('admin.issue-categories.index'));

        $this->assertDatabaseHas('issue_categories', ['id' => $category->id, 'name' => 'Fasilitas Rusak', 'is_active' => false]);
        $this->assertFalse(Route::has('admin.issue-categories.destroy'));
    }

    public function test_reference_names_are_required_unique_and_ignore_the_current_record_on_update(): void
    {
        $admin = $this->admin();
        $feature = AccessibilityFeature::factory()->create(['name' => 'Ramp']);
        $category = IssueCategory::factory()->create(['name' => 'Rusak']);

        $this->actingAs($admin)->post(route('admin.features.store'), ['name' => 'Ramp'])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post(route('admin.issue-categories.store'), [])->assertSessionHasErrors('name');
        $this->actingAs($admin)->put(route('admin.features.update', $feature), ['name' => 'Ramp', 'is_active' => 1])->assertSessionHasNoErrors();
        $this->actingAs($admin)->put(route('admin.issue-categories.update', $category), ['name' => 'Rusak', 'is_active' => 1])->assertSessionHasNoErrors();
    }

    public function test_reference_indexes_show_truthful_empty_states(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.features.index'))->assertSee('Belum ada fasilitas');
        $this->actingAs($admin)->get(route('admin.issue-categories.index'))->assertSee('Belum ada kategori');
    }

    public function test_non_admins_cannot_access_reference_data_endpoints(): void
    {
        $feature = AccessibilityFeature::factory()->create();
        $category = IssueCategory::factory()->create();
        $uris = [
            ['get', route('admin.features.index')], ['get', route('admin.features.create')], ['post', route('admin.features.store')],
            ['get', route('admin.features.edit', $feature)], ['put', route('admin.features.update', $feature)], ['patch', route('admin.features.deactivate', $feature)],
            ['get', route('admin.issue-categories.index')], ['get', route('admin.issue-categories.create')], ['post', route('admin.issue-categories.store')],
            ['get', route('admin.issue-categories.edit', $category)], ['put', route('admin.issue-categories.update', $category)], ['patch', route('admin.issue-categories.deactivate', $category)],
        ];

        foreach ($uris as [$method, $uri]) {
            $this->actingAs(User::factory()->create(['role' => 'reporter']))->{$method}($uri)->assertForbidden();
        }
    }
}
