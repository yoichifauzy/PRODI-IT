<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $month = $this->resolveMonth((string) $request->query('month', ''));

        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();

        $events = AcademicEvent::query()
            ->published()
            ->where('start_date', '<=', $monthEnd->toDateString())
            ->where(function ($q) use ($monthStart): void {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $monthStart->toDateString());
            })
            ->orderBy('start_date')
            ->orderBy('title')
            ->get();

        return view('public.calendar', [
            'events' => $events,
            'month' => $month,
            'prevMonth' => $month->subMonthNoOverflow(),
            'nextMonth' => $month->addMonthNoOverflow(),
            'selectedMonth' => $month->format('Y-m'),
        ]);
    }

    public function show(AcademicEvent $academicEvent): View
    {
        abort_unless($academicEvent->is_published, 404);

        return view('public.calendar-event-detail', [
            'event' => $academicEvent,
        ]);
    }

    private function resolveMonth(string $month): CarbonImmutable
    {
        if (preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            try {
                $parsed = CarbonImmutable::createFromFormat('Y-m', $month);

                if ($parsed instanceof CarbonImmutable) {
                    return $parsed->startOfMonth();
                }
            } catch (\Throwable) {
                return CarbonImmutable::now()->startOfMonth();
            }
        }

        return CarbonImmutable::now()->startOfMonth();
    }
}
