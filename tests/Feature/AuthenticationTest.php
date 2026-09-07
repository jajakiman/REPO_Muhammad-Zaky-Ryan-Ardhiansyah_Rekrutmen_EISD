<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_lists_only_active_campuses(): void
    {
        Campus::factory()->create(['name' => 'Kampus Aktif', 'is_active' => true]);
        Campus::factory()->create(['name' => 'Kampus Nonaktif', 'is_active' => false]);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Kampus Aktif')
            ->assertDontSee('Kampus Nonaktif');
    }

    public function test_public_registration_creates_an_active_reporter_with_hashed_password(): void
    {
        $campus = Campus::factory()->create();

        $response = $this->post(route('register'), [
            'name' => 'Rina Pelapor',
            'email' => 'rina@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'affiliation_type' => 'student',
            'campus_id' => $campus->id,
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('login'))->assertSessionHas('success');
        $user = User::where('email', 'rina@example.test')->firstOrFail();
        $this->assertSame('reporter', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertGuest();
    }

    public function test_campus_is_required_for_campus_affiliations(): void
    {
        foreach (['student', 'lecturer', 'staff'] as $affiliation) {
            $this->post(route('register'), $this->registrationData($affiliation))
                ->assertSessionHasErrors('campus_id');
        }
    }

    public function test_visitor_may_register_without_a_campus(): void
    {
        $this->post(route('register'), $this->registrationData('visitor'))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['email' => 'visitor@example.test', 'campus_id' => null]);
    }

    public function test_registration_rejects_duplicate_email_and_inactive_campus(): void
    {
        User::factory()->create(['email' => 'visitor@example.test']);
        $inactiveCampus = Campus::factory()->create(['is_active' => false]);

        $this->post(route('register'), $this->registrationData('student') + ['campus_id' => $inactiveCampus->id])
            ->assertSessionHasErrors(['email', 'campus_id'])
            ->assertSessionHasInput('name', 'Visitor Baru');
    }

    private function registrationData(string $affiliation): array
    {
        return [
            'name' => 'Visitor Baru',
            'email' => 'visitor@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'affiliation_type' => $affiliation,
        ];
    }
}
