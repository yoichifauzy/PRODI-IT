<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TracerAlumniSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TracerAlumniSlideController extends Controller
{
    public function index()
    {
        $slides = TracerAlumniSlide::orderBy('order')->get();
        return view('admin.tracer-alumni-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.tracer-alumni-slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer'
        ]);

        $imagePath = $request->file('image')->store('tracer_alumni_slides', 'public');

        TracerAlumniSlide::create([
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.tracer-alumni-slides.index')->with('success', 'Slide berhasil ditambahkan.');
    }

    public function edit(TracerAlumniSlide $tracerAlumniSlide)
    {
        return view('admin.tracer-alumni-slides.edit', compact('tracerAlumniSlide'));
    }

    public function update(Request $request, TracerAlumniSlide $tracerAlumniSlide)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            if ($tracerAlumniSlide->image_path && Storage::disk('public')->exists($tracerAlumniSlide->image_path)) {
                Storage::disk('public')->delete($tracerAlumniSlide->image_path);
            }
            $imagePath = $request->file('image')->store('tracer_alumni_slides', 'public');
            $tracerAlumniSlide->image_path = $imagePath;
        }

        $tracerAlumniSlide->is_active = $request->has('is_active') ? $request->is_active : false;
        $tracerAlumniSlide->order = $request->order ?? 0;
        $tracerAlumniSlide->save();

        return redirect()->route('admin.tracer-alumni-slides.index')->with('success', 'Slide berhasil diperbarui.');
    }

    public function destroy(TracerAlumniSlide $tracerAlumniSlide)
    {
        if ($tracerAlumniSlide->image_path && Storage::disk('public')->exists($tracerAlumniSlide->image_path)) {
            Storage::disk('public')->delete($tracerAlumniSlide->image_path);
        }
        $tracerAlumniSlide->delete();

        return redirect()->route('admin.tracer-alumni-slides.index')->with('success', 'Slide berhasil dihapus.');
    }
}
