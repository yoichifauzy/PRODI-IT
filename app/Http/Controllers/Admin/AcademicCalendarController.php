<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use Carbon\Carbon;

class AcademicCalendarController extends Controller
{
    public function index()
    {
        $calendars = AcademicCalendar::latest('start_date')->get();
        return view('admin.academic-calendars.index', compact('calendars'));
    }

    public function create()
    {
        return view('admin.academic-calendars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:UAS,UTS,Lainnya',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['created_by'] = Auth::id();

        $calendar = AcademicCalendar::create($validated);

        try {
            // Create event in Google Calendar
            $gEvent = new GoogleEvent;
            $gEvent->name = $calendar->title;
            $gEvent->description = $calendar->description;
            $gEvent->startDate = Carbon::parse($calendar->start_date);
            if ($calendar->end_date) {
                $gEvent->endDate = Carbon::parse($calendar->end_date)->addDay();
            } else {
                $gEvent->endDate = Carbon::parse($calendar->start_date)->addDay();
            }
            $gEvent = $gEvent->save();

            $calendar->update(['google_calendar_id' => $gEvent->id]);
        } catch (\Exception $e) {
            return redirect()->route('admin.academic-calendars.index')
                ->with('success', 'Kalender akademik ditambahkan, namun gagal sinkronisasi ke Google Calendar: ' . $e->getMessage());
        }

        return redirect()->route('admin.academic-calendars.index')->with('success', 'Kalender akademik ditambahkan dan disinkronisasi ke Google Calendar.');
    }

    public function edit(AcademicCalendar $academicCalendar)
    {
        return view('admin.academic-calendars.edit', compact('academicCalendar'));
    }

    public function update(Request $request, AcademicCalendar $academicCalendar)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:UAS,UTS,Lainnya',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $academicCalendar->update($validated);

        try {
            if ($academicCalendar->google_calendar_id) {
                $gEvent = GoogleEvent::find($academicCalendar->google_calendar_id);
                if ($gEvent) {
                    $gEvent->name = $academicCalendar->title;
                    $gEvent->description = $academicCalendar->description;

                    $gEvent->startDate = Carbon::parse($academicCalendar->start_date);
                    if ($academicCalendar->end_date) {
                        $gEvent->endDate = Carbon::parse($academicCalendar->end_date)->addDay();
                    } else {
                        $gEvent->endDate = Carbon::parse($academicCalendar->start_date)->addDay();
                    }
                    $gEvent->save();
                }
            } else {
                $gEvent = new GoogleEvent;
                $gEvent->name = $academicCalendar->title;
                $gEvent->description = $academicCalendar->description;
                
                $gEvent->startDate = Carbon::parse($academicCalendar->start_date);
                if ($academicCalendar->end_date) {
                    $gEvent->endDate = Carbon::parse($academicCalendar->end_date)->addDay();
                } else {
                    $gEvent->endDate = Carbon::parse($academicCalendar->start_date)->addDay();
                }
                $gEvent = $gEvent->save();

                $academicCalendar->update(['google_calendar_id' => $gEvent->id]);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.academic-calendars.index')
                ->with('success', 'Kalender akademik diperbarui, namun gagal sinkronisasi ke Google Calendar: ' . $e->getMessage());
        }

        return redirect()->route('admin.academic-calendars.index')->with('success', 'Kalender akademik berhasil diperbarui.');
    }

    public function destroy(AcademicCalendar $academicCalendar)
    {
        try {
            if ($academicCalendar->google_calendar_id) {
                $gEvent = GoogleEvent::find($academicCalendar->google_calendar_id);
                if ($gEvent) {
                    $gEvent->delete();
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Calendar Error (Delete): ' . $e->getMessage());
        }

        $academicCalendar->delete();

        return redirect()->route('admin.academic-calendars.index')->with('success', 'Kalender akademik berhasil dihapus.');
    }
}
