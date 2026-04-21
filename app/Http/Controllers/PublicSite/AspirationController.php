<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreAspirationRequest;
use App\Models\Aspiration;
use Illuminate\Http\RedirectResponse;

class AspirationController extends Controller
{
    public function store(StoreAspirationRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['ip_address'] = $request->ip();
        $payload['user_agent'] = (string) $request->userAgent();

        Aspiration::query()->create($payload);

        return redirect()
            ->route('home')
            ->with('success', 'Aspirasi Anda telah dikirim. Terima kasih atas masukan Anda.');
    }
}
