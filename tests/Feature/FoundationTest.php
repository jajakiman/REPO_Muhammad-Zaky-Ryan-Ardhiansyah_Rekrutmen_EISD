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
}
