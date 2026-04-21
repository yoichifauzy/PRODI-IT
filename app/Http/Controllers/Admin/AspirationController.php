<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $search = (string) $request->query('q', '');

        $aspirations = Aspiration::query()
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($query) use ($search): void {
                    $query
                        ->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.aspirations.index', [
            'aspirations' => $aspirations,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(Aspiration $aspiration, Request $request): View
    {
        if ($aspiration->status === 'unread' && $request->user() !== null) {
            $aspiration->markAsRead($request->user());
            $aspiration->refresh();
        }

        return view('admin.aspirations.show', [
            'aspiration' => $aspiration,
        ]);
    }

    public function update(Request $request, Aspiration $aspiration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:unread,read,archived'],
        ]);

        $status = $validated['status'];
        $payload = ['status' => $status];

        if ($status === 'read') {
            $payload['read_at'] = now();
            $payload['read_by'] = $request->user()?->id;
        }

        if ($status === 'unread') {
            $payload['read_at'] = null;
            $payload['read_by'] = null;
        }

        $aspiration->update($payload);

        return redirect()
            ->route('admin.aspirations.show', $aspiration)
            ->with('success', 'Status aspirasi berhasil diperbarui.');
    }

    public function destroy(Aspiration $aspiration): RedirectResponse
    {
        $aspiration->delete();

        return redirect()
            ->route('admin.aspirations.index')
            ->with('success', 'Aspirasi berhasil dihapus.');
    }
}
