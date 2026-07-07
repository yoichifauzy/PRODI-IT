<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = AssetLink::with('creator')->latest()->get();
        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('admin.documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:2048',
        ]);

        AssetLink::create([
            'name'       => $validated['name'],
            'url'        => $validated['url'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(AssetLink $document)
    {
        return view('admin.documents.edit', compact('document'));
    }

    public function update(Request $request, AssetLink $document)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:2048',
        ]);

        $document->update($validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(AssetLink $document)
    {
        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
