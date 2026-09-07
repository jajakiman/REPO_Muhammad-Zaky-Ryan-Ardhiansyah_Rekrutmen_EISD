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

    public function test_auth_pages_use_branded_responsive_placement_without_emoji(): void
    {
        Campus::factory()->create(['name' => 'Kampus Aktif']);

        foreach ([route('login'), route('register')] as $route) {
            $response = $this->get($route);

            $response->assertOk()
                ->assertSee('auth-shell', false)
                ->assertSee('auth-panel', false)
                ->assertSee('logo-mark.webp', false)
                ->assertDontSee('🚀')
                ->assertDontSee('✨')
                ->assertDontSee('✅');
        }
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

    public function test_login_failure_uses_the_same_message_for_unknown_email_wrong_password_and_inactive_account(): void
    {
        User::factory()->create(['email' => 'active@example.test', 'password' => 'correct-password']);
        User::factory()->create(['email' => 'inactive@example.test', 'password' => 'correct-password', 'is_active' => false]);

        foreach ([
            ['missing@example.test', 'correct-password'],
            ['active@example.test', 'wrong-password'],
            ['inactive@example.test', 'correct-password'],
        ] as [$email, $password]) {
            $this->post(route('login'), compact('email', 'password'))
                ->assertSessionHasErrors(['email' => 'Email atau password tidak sesuai.']);
            $this->assertGuest();
        }
    }

    public function test_successful_login_regenerates_the_session_and_redirects_each_role(): void
    {
        foreach ([
            'reporter' => 'reporter.dashboard',
            'officer' => 'officer.dashboard',
            'admin' => 'admin.dashboard',
        ] as $role => $destination) {
            $user = User::factory()->create([
                'email' => $role.'@example.test',
                'password' => 'correct-password',
                'role' => $role,
            ]);
            $this->withSession(['session_marker' => $role]);
            $oldSessionId = session()->getId();

            $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password'])
                ->assertRedirect(route($destination));

            $this->assertAuthenticatedAs($user);
            $this->assertNotSame($oldSessionId, session()->getId());
            auth()->logout();
        }
    }

    public function test_login_ignores_an_intended_url_and_always_redirects_to_the_users_role_route(): void
    {
        $reporter = User::factory()->create([
            'email' => 'reporter@example.test',
            'password' => 'correct-password',
            'role' => 'reporter',
        ]);

        $this->withSession(['url.intended' => route('admin.dashboard')])
            ->post(route('login'), ['email' => $reporter->email, 'password' => 'correct-password'])
            ->assertRedirect(route('reporter.dashboard'));
    }

    public function test_login_is_locked_after_five_failed_attempts_for_the_same_email_and_ip(): void
    {
        $user = User::factory()->create([
            'email' => 'locked@example.test',
            'password' => 'correct-password',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password'])
                ->assertSessionHasErrors(['email' => 'Email atau password tidak sesuai.']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password'])
            ->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan masuk. Silakan coba lagi nanti.']);
        $this->assertGuest();
    }

    public function test_successful_login_resets_the_failed_attempt_counter(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => 'correct-password',
        ]);

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect(route('reporter.dashboard'));
        auth()->logout();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect(route('reporter.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_invalidates_session_and_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['private_value' => 'secret']);
        $oldToken = session()->token();

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertFalse(session()->has('private_value'));
        $this->assertNotSame($oldToken, session()->token());
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
