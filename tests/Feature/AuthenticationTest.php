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
                ->assertSee('bg-navy-950', false)
                ->assertSee('logo-mark.webp', false)
                ->assertDontSee('site-header', false)
                ->assertDontSee('site-footer', false)
                ->assertDontSee('🚀')
                ->assertDontSee('✨')
                ->assertDontSee('✅');
        }
    }

    public function test_auth_pages_fill_the_viewport_and_return_to_the_home_page(): void
    {
        foreach ([route('login'), route('register')] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('min-h-[100dvh]', false)
                ->assertSee('Kembali ke Halaman Utama')
                ->assertSee('href="'.route('home').'"', false)
                ->assertDontSee('Kembali ke peta kampus');
        }
    }

    public function test_auth_forms_mark_required_fields_and_offer_password_visibility_controls(): void
    {
        Campus::factory()->create();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('required-mark', false)
            ->assertSee('data-password-toggle="password"', false)
            ->assertSee('aria-label="Tampilkan password"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSeeInOrder([
                'Nama lengkap',
                'required-mark',
                'Email',
                'required-mark',
                'Password',
                'required-mark',
            ], false)
            ->assertSee('data-password-toggle="password"', false)
            ->assertSee('data-password-toggle="password_confirmation"', false);
    }

    public function test_password_visibility_controls_use_eye_icons_without_visible_text(): void
    {
        foreach ([route('login'), route('register')] as $route) {
            $response = $this->get($route);

            $response->assertOk()
                ->assertSee('data-eye-open', false)
                ->assertSee('data-eye-closed', false)
                ->assertDontSee('>Lihat</button>', false)
                ->assertDontSee('>Sembunyikan</button>', false);
        }
    }

    public function test_standalone_auth_layout_keeps_flash_feedback(): void
    {
        $this->withSession(['success' => 'Registrasi berhasil. Silakan masuk ke AksesLoka.'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Registrasi berhasil. Silakan masuk ke AksesLoka.')
            ->assertSee('role="status"', false);
    }

    public function test_registration_exposes_affiliation_dependent_campus_field_and_error_summary(): void
    {
        Campus::factory()->create();

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('data-affiliation-campus', false)
            ->assertSee('data-campus-field', false)
            ->assertSee('data-required-for="student,lecturer,staff"', false);

        $this->from(route('register'))->post(route('register'), [])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors();

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('form-error-summary', false)
            ->assertSee('Mohon periksa kembali formulir Anda.');
    }

    public function test_registration_uses_interactive_native_select_components(): void
    {
        Campus::factory()->create(['name' => 'Kampus Aktif']);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('select-shell', false)
            ->assertSee('select-chevron', false)
            ->assertSee('name="affiliation_type"', false)
            ->assertSee('name="campus_id"', false);
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

        $user = User::where('email', 'rina@example.test')->firstOrFail();
        $response->assertOk()
            ->assertSee('data-auth-success-dialog', false)
            ->assertSee('data-redirect-url="'.route('reporter.dashboard').'"', false)
            ->assertSee('data-auto-close="3000"', false)
            ->assertDontSee('>OK</button>', false);
        $this->assertSame('reporter', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertAuthenticatedAs($user);
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
        $response = $this->post(route('register'), $this->registrationData('visitor'));
        $response->assertOk()->assertSee('data-auth-success-dialog', false);

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
                ->assertOk()
                ->assertSee('data-auth-success-dialog', false)
                ->assertSee('data-redirect-url="'.route($destination).'"', false)
                ->assertDontSee('>OK</button>', false);

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
            ->assertOk()
            ->assertSee('data-redirect-url="'.route('reporter.dashboard').'"', false);
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
            ->assertOk();
        auth()->logout();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password'])
            ->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_invalidates_session_and_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['private_value' => 'secret']);
        $oldToken = session()->token();

        $this->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success_modal', true)
            ->assertSessionMissing('success_modal_auto_close');

        $this->assertGuest();
        $this->assertFalse(session()->has('private_value'));
        $this->assertNotSame($oldToken, session()->token());
    }

    public function test_each_authenticated_role_sees_an_accessible_logout_confirmation(): void
    {
        foreach (['reporter', 'officer', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get(route($role.'.dashboard'))
                ->assertOk()
                ->assertSee('data-logout-form', false)
                ->assertSee('data-logout-dialog', false)
                ->assertSee('Konfirmasi Keluar')
                ->assertSee('Apakah Anda yakin ingin keluar dari AksesLoka?')
                ->assertSee('Batal')
                ->assertSee('Ya, Keluar');

            auth()->logout();
        }
    }

    public function test_success_modal_is_accessible_and_only_auto_closes_when_requested(): void
    {
        $this->withSession([
            'success' => 'Berhasil masuk.',
            'success_modal' => true,
            'success_modal_auto_close' => true,
        ])->get(route('home'))
            ->assertOk()
            ->assertSee('<dialog', false)
            ->assertSee('data-success-dialog', false)
            ->assertSee('data-auto-close="3000"', false)
            ->assertSee('>OK</button>', false);

        $this->withSession([
            'success' => 'Data berhasil ditambahkan.',
            'success_modal' => true,
            'success_modal_auto_close' => false,
        ])->get(route('home'))
            ->assertOk()
            ->assertSee('data-success-dialog', false)
            ->assertDontSee('data-auto-close="3000"', false);
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
