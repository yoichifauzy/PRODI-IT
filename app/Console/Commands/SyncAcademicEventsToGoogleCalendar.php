<?php

namespace App\Console\Commands;

use App\Models\AcademicEvent;
use App\Services\GoogleCalendarSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAcademicEventsToGoogleCalendar extends Command
{
    protected $signature = 'academic-events:sync-google {--published-only : Sync only published events}';

    protected $description = 'Sync academic events to Google Calendar using configured service account';

    public function handle(GoogleCalendarSyncService $googleCalendarSyncService): int
    {
        if (!(bool) config('services.google_calendar.enabled', false)) {
            $this->error('Google Calendar sync belum aktif. Set GOOGLE_CALENDAR_ENABLED=true atau GOOGLE_CALENDAR_SYNC_ENABLED=true.');

            return self::FAILURE;
        }

        $calendarId = trim((string) config('services.google_calendar.calendar_id', ''));
        if ($calendarId === '') {
            $this->error('GOOGLE_CALENDAR_ID belum diisi.');

            return self::FAILURE;
        }

        $serviceAccountPath = trim((string) config('services.google_calendar.service_account_json', ''));
        $absoluteServiceAccountPath = $this->resolvePath($serviceAccountPath);
        if ($absoluteServiceAccountPath === '' || !is_file($absoluteServiceAccountPath)) {
            $this->error('File service account tidak ditemukan: ' . $serviceAccountPath);

            return self::FAILURE;
        }

        $eventsQuery = AcademicEvent::query()->orderBy('id');
        if ((bool) $this->option('published-only')) {
            $eventsQuery->where('is_published', true);
        }

        $events = $eventsQuery->get();
        if ($events->isEmpty()) {
            $this->info('Tidak ada event akademik untuk disinkronkan.');

            return self::SUCCESS;
        }

        $this->info('Mulai sinkronisasi ' . $events->count() . ' event ke Google Calendar...');
        $progressBar = $this->output->createProgressBar($events->count());
        $progressBar->start();

        $synced = 0;
        $removed = 0;
        $failed = 0;

        foreach ($events as $event) {
            $url = $googleCalendarSyncService->syncOrDeleteAcademicEvent($event);

            if ($event->is_published) {
                if ($url !== null && $url !== '') {
                    if ((string) $event->google_event_url !== $url) {
                        $event->forceFill(['google_event_url' => $url])->save();
                    }
                    $synced++;
                } else {
                    $failed++;
                }
            } else {
                if (!empty($event->google_event_url)) {
                    $event->forceFill(['google_event_url' => null])->save();
                }
                $removed++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info('Sinkronisasi selesai.');
        $this->line('Tersinkron: ' . $synced);
        $this->line('Dihapus dari Google (unpublished): ' . $removed);
        $this->line('Gagal sinkron: ' . $failed);

        if ($failed > 0) {
            $this->warn('Lihat detail error di file log: storage/logs/laravel.log');
            Log::warning('Academic events Google sync finished with failures.', [
                'synced' => $synced,
                'removed' => $removed,
                'failed' => $failed,
            ]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if ($path === '') {
            return '';
        }

        if (preg_match('/^(?:[A-Za-z]:\\\\|\/)/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }
}
