<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporterProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporter_can_view_profile_with_read_only_email_and_only_active_campuses(): void
    {
        $user = User::factory()->create(['email' => 'reporter@example.test']);
        Campus::factory()->create(['name' => 'Kampus Aktif', 'is_active' => true]);
        Campus::factory()->create(['name' => 'Kampus Nonaktif', 'is_active' => false]);

        $this->actingAs($user)->get(route('reporter.profile.edit'))
            ->assertOk()
            ->assertSee('reporter@example.test')
            ->assertSee('readonly', false)
            ->assertSee('Kampus Aktif')
            ->assertDontSee('Kampus Nonaktif');
    }

    public function test_reporter_can_update_name_and_campus_affiliation_without_changing_email(): void
    {
        $user = User::factory()->create(['email' => 'tetap@example.test', 'affiliation_type' => 'visitor']);
        $campus = Campus::factory()->create();

        $this->actingAs($user)->put(route('reporter.profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'ubah@example.test',
            'affiliation_type' => 'lecturer',
            'campus_id' => $campus->id,
        ])->assertRedirect(route('reporter.profile.edit'))
            ->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('tetap@example.test', $user->email);
        $this->assertSame('lecturer', $user->affiliation_type);
        $this->assertSame($campus->id, $user->campus_id);
    }

    public function test_profile_requires_an_active_campus_for_campus_affiliations(): void
    {
        $user = User::factory()->create();
        $inactiveCampus = Campus::factory()->create(['is_active' => false]);

        foreach (['student', 'lecturer', 'staff'] as $affiliation) {
            $this->actingAs($user)->put(route('reporter.profile.update'), [
                'name' => 'Pelapor',
                'affiliation_type' => $affiliation,
            ])->assertSessionHasErrors('campus_id');
        }

        $this->actingAs($user)->put(route('reporter.profile.update'), [
            'name' => 'Pelapor',
            'affiliation_type' => 'student',
            'campus_id' => $inactiveCampus->id,
        ])->assertSessionHasErrors('campus_id');
    }

    public function test_visitor_may_clear_their_campus(): void
    {
        $campus = Campus::factory()->create();
        $user = User::factory()->create(['campus_id' => $campus->id, 'affiliation_type' => 'student']);

        $this->actingAs($user)->put(route('reporter.profile.update'), [
            'name' => $user->name,
            'affiliation_type' => 'visitor',
            'campus_id' => null,
        ])->assertRedirect(route('reporter.profile.edit'));

        $this->assertNull($user->fresh()->campus_id);
    }

    public function test_non_reporters_cannot_access_reporter_profile_routes(): void
    {
        foreach (['officer', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('reporter.profile.edit'))->assertForbidden();
            $this->actingAs($user)->put(route('reporter.profile.update'), [])->assertForbidden();
        }
    }
}
