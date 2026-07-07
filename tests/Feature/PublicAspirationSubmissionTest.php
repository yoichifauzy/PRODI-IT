<?php

use App\Models\Aspiration;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores an aspiration without optional email and nim', function (): void {
    $response = $this->post(route('aspirations.store'), [
        'email' => '',
        'nim' => '',
        'subject' => 'Cek',
        'message' => 'Assa',
    ]);

    $response->assertRedirect(route('home') . '#aspirasi');

    $this->assertDatabaseCount('aspirations', 1);
    $this->assertDatabaseHas('aspirations', [
        'email' => null,
        'nim' => null,
        'subject' => 'Cek',
        'message' => 'Assa',
    ]);

    expect(Aspiration::query()->first())
        ->not->toBeNull()
        ->and(Aspiration::query()->first()->email)->toBeNull()
        ->and(Aspiration::query()->first()->nim)->toBeNull();
});
