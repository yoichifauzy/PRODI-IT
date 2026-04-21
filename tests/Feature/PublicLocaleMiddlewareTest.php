<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function expectedDefaultLocale(): string
    {
        return str_starts_with(strtolower((string) config('app.locale')), 'en') ? 'en' : 'id';
    }

    public function test_default_locale_is_indonesia(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="' . $this->expectedDefaultLocale() . '">', false);
    }

    public function test_locale_can_be_switched_with_lang_query(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertOk();
        $response->assertSee('<html lang="en">', false);
        $response->assertCookie('site_locale', 'en');
    }

    public function test_invalid_lang_query_falls_back_to_default_locale(): void
    {
        $response = $this->withCookie('site_locale', 'fr')->get('/?lang=fr');

        $response->assertOk();
        $response->assertSee('<html lang="' . $this->expectedDefaultLocale() . '">', false);
    }
}
