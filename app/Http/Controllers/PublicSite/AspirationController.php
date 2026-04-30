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

        // Prevent duplicate submissions using the message content plus optional
        // identifiers within a short window (likely caused by double-click or reload).
        $existsQuery = Aspiration::query()
            ->where('subject', $payload['subject'])
            ->where('message', $payload['message'])
            ->where('created_at', '>=', now()->subMinutes(10));

        $existsQuery->when(!empty($payload['nim']), fn($query) => $query->where('nim', $payload['nim']), fn($query) => $query->whereNull('nim'));
        $existsQuery->when(!empty($payload['email']), fn($query) => $query->where('email', $payload['email']), fn($query) => $query->whereNull('email'));

        if ($existsQuery->exists()) {
            return redirect()->to(route('home') . '#aspirasi')
                ->with('success', 'Aspirasi serupa sudah dikirim dalam beberapa menit terakhir.');
        }

        Aspiration::query()->create($payload);

        // Redirect back to the home page anchored to the aspirasi section
        // so the success alert is visible immediately after submit.
        return redirect()->to(route('home') . '#aspirasi')
            ->with('success', 'Aspirasi Anda telah dikirim. Terima kasih atas masukan Anda.');
    }
}
