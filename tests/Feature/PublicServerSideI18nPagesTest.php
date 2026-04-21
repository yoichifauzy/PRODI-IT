<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicServerSideI18nPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_server_side_english_text_when_locale_is_en(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertOk();
        $response->assertSee('About Us');
        $response->assertSee('Vision and Mission');
        $response->assertSee('Submit Your Aspiration');
    }

    public function test_calendar_page_uses_server_side_english_text_when_locale_is_en(): void
    {
        $response = $this->get(route('calendar.index', ['lang' => 'en']));

        $response->assertOk();
        $response->assertSee('Academic Calendar');
        $response->assertSee('Apply');
        $response->assertSee('Previous Month');
    }
}
