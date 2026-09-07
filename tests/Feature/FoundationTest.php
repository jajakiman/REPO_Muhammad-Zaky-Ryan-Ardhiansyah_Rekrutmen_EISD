<?php

namespace Tests\Feature;

use Tests\TestCase;

class FoundationTest extends TestCase
{
    public function test_home_page_exposes_the_brand_and_skip_link(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('AksesLoka')
            ->assertSee('Langsung ke konten utama')
            ->assertSee('href="#main-content"', false);
    }

    public function test_home_page_does_not_advertise_an_unavailable_map(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Lihat peta');
    }

    public function test_flash_messages_use_appropriate_live_region_roles(): void
    {
        $this->withSession([
            'success' => 'Data tersimpan.',
            'error' => 'Data gagal disimpan.',
        ])->get('/')
            ->assertSee('flash-positive" role="status', false)
            ->assertSee('flash-critical" role="alert', false);
    }

    public function test_example_environment_uses_database_free_web_defaults(): void
    {
        $environment = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('SESSION_DRIVER=file', $environment);
        $this->assertStringContainsString('CACHE_STORE=file', $environment);
    }

    public function test_home_page_uses_the_public_css_asset_without_vite(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.asset('css/app.css').'"', false)
            ->assertDontSee('http://localhost:5173');

        $this->assertFileExists(public_path('css/app.css'));
    }

    public function test_project_configuration_has_no_node_or_vite_runtime(): void
    {
        $composer = file_get_contents(base_path('composer.json'));
        $environment = file_get_contents(base_path('.env.example'));
        $readme = file_get_contents(base_path('README.md'));

        $this->assertStringNotContainsString('npx', $composer);
        $this->assertStringNotContainsString('npm ', $composer);
        $this->assertStringNotContainsString('VITE_', $environment);
        $this->assertStringNotContainsString('Node.js', $readme);
        $this->assertStringNotContainsString('npm ', $readme);
        $this->assertStringNotContainsString('Vite', $readme);
    }
}
