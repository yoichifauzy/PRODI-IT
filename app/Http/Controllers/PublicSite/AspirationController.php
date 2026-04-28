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

        // Prevent duplicate submissions: consider submissions with identical
        // full_name, nim (nullable), subject and message within a short window
        // as duplicates (likely caused by double-click or reload).
        $existsQuery = Aspiration::query()
            ->where('full_name', $payload['full_name'])
            ->where('subject', $payload['subject'])
            ->where('message', $payload['message'])
            ->where('created_at', '>=', now()->subMinutes(10));

        if (!empty($payload['nim'])) {
            $existsQuery->where('nim', $payload['nim']);
        } else {
            $existsQuery->whereNull('nim');
        }

        if (!empty($payload['email'])) {
            $existsQuery->where('email', $payload['email']);
        } else {
            $existsQuery->whereNull('email');
        }

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
