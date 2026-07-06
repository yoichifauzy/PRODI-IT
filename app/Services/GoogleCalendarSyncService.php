<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AcademicEvent;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarSyncService
{
    private ?string $accessToken = null;
    private int $accessTokenExpiresAt = 0;

    // =========================================================
    //  PUBLIC: Activity (Kegiatan)
    // =========================================================

    public function syncOrDeleteActivity(Activity $activity): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        return $this->upsertActivity($activity);
    }

    public function deleteActivity(Activity $activity): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        try {
            $existing = $this->findRemoteActivityEvent($activity);
            if ($existing === null) {
                return;
            }

            $eventId = (string) ($existing['id'] ?? '');
            if ($eventId === '') {
                return;
            }

            $this->authorizedRequest()
                ->delete($this->calendarBaseUrl() . '/events/' . rawurlencode($eventId))
                ->throw();
        } catch (RequestException $e) {
            Log::warning('Failed deleting Activity from Google Calendar (HTTP error).', [
                'activity_id' => $activity->id,
                'status'      => $e->response?->status(),
                'response'    => $e->response?->body(),
                'message'     => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed deleting Activity from Google Calendar.', [
                'activity_id' => $activity->id,
                'message'     => $e->getMessage(),
            ]);
        }
    }

    private function upsertActivity(Activity $activity): ?string
    {
        try {
            $payload  = $this->buildActivityPayload($activity);
            $existing = $this->findRemoteActivityEvent($activity);

            if ($existing !== null) {
                $eventId = (string) ($existing['id'] ?? '');
                if ($eventId !== '') {
                    $response = $this->authorizedRequest()
                        ->put($this->calendarBaseUrl() . '/events/' . rawurlencode($eventId), $payload)
                        ->throw()
                        ->json();

                    return (string) ($response['htmlLink'] ?? '');
                }
            }

            $response = $this->authorizedRequest()
                ->post($this->calendarBaseUrl() . '/events', $payload)
                ->throw()
                ->json();

            return (string) ($response['htmlLink'] ?? '');
        } catch (RequestException $e) {
            Log::warning('Failed syncing Activity to Google Calendar (HTTP error).', [
                'activity_id' => $activity->id,
                'status'      => $e->response?->status(),
                'response'    => $e->response?->body(),
                'message'     => $e->getMessage(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::warning('Failed syncing Activity to Google Calendar.', [
                'activity_id' => $activity->id,
                'message'     => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildActivityPayload(Activity $activity): array
    {
        $detailUrl = route('public.activities.show', ['activity' => $activity->id]);

        $descriptionParts = array_filter([
            $activity->description,
            'Detail Kegiatan: ' . $detailUrl,
        ]);

        // Jika ada start_at/end_at, gunakan dateTime (ada jam).
        // Jika tidak, gunakan date-only (seharian penuh).
        if ($activity->start_at !== null) {
            $start = [
                'dateTime' => $activity->start_at->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Jakarta',
            ];
            $end = [
                'dateTime' => ($activity->end_at ?? $activity->start_at->copy()->addHours(2))
                    ->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Jakarta',
            ];
        } else {
            $start = [
                'date'     => $activity->event_date->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ];
            $end = [
                'date'     => $activity->event_date->copy()->addDay()->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ];
        }

        return [
            'summary'     => $activity->title,
            'description' => implode("\n\n", $descriptionParts),
            'location'    => $activity->location,
            'start'       => $start,
            'end'         => $end,
            'source'      => [
                'title' => config('app.name') . ' - Kegiatan',
                'url'   => $detailUrl,
            ],
            'extendedProperties' => [
                'private' => [
                    'appActivityId' => (string) $activity->id,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findRemoteActivityEvent(Activity $activity): ?array
    {
        $response = $this->authorizedRequest()
            ->get($this->calendarBaseUrl() . '/events', [
                'privateExtendedProperty' => 'appActivityId=' . $activity->id,
                'maxResults'              => 1,
                'singleEvents'            => 'false',
                'showDeleted'             => 'false',
            ])
            ->throw()
            ->json();

        $items = $response['items'] ?? [];

        if (!is_array($items) || !isset($items[0]) || !is_array($items[0])) {
            return null;
        }

        return $items[0];
    }

    // =========================================================
    //  PUBLIC: AcademicEvent (dipertahankan untuk backward compat)
    // =========================================================

    public function syncOrDeleteAcademicEvent(AcademicEvent $event): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        if (!$event->is_published) {
            $this->deleteAcademicEvent($event);
            return null;
        }

        return $this->upsertAcademicEvent($event);
    }

    public function deleteAcademicEvent(AcademicEvent $event): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        try {
            $existing = $this->findRemoteAcademicEvent($event);
            if ($existing === null) {
                return;
            }

            $eventId = (string) ($existing['id'] ?? '');
            if ($eventId === '') {
                return;
            }

            $this->authorizedRequest()
                ->delete($this->calendarBaseUrl() . '/events/' . rawurlencode($eventId))
                ->throw();
        } catch (RequestException $e) {
            Log::warning('Failed deleting event from Google Calendar (HTTP error).', [
                'academic_event_id' => $event->id,
                'status'            => $e->response?->status(),
                'response'          => $e->response?->body(),
                'message'           => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed deleting event from Google Calendar.', [
                'academic_event_id' => $event->id,
                'message'           => $e->getMessage(),
            ]);
        }
    }

    private function upsertAcademicEvent(AcademicEvent $event): ?string
    {
        try {
            $payload  = $this->buildAcademicEventPayload($event);
            $existing = $this->findRemoteAcademicEvent($event);

            if ($existing !== null) {
                $eventId = (string) ($existing['id'] ?? '');
                if ($eventId !== '') {
                    $response = $this->authorizedRequest()
                        ->put($this->calendarBaseUrl() . '/events/' . rawurlencode($eventId), $payload)
                        ->throw()
                        ->json();

                    return (string) ($response['htmlLink'] ?? '');
                }
            }

            $response = $this->authorizedRequest()
                ->post($this->calendarBaseUrl() . '/events', $payload)
                ->throw()
                ->json();

            return (string) ($response['htmlLink'] ?? '');
        } catch (RequestException $e) {
            Log::warning('Failed syncing academic event to Google Calendar (HTTP error).', [
                'academic_event_id' => $event->id,
                'status'            => $e->response?->status(),
                'response'          => $e->response?->body(),
                'message'           => $e->getMessage(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::warning('Failed syncing academic event to Google Calendar.', [
                'academic_event_id' => $event->id,
                'message'           => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAcademicEventPayload(AcademicEvent $event): array
    {
        $startDate = $event->start_date?->format('Y-m-d');
        $endDate   = ($event->end_date ?? $event->start_date)?->copy()->addDay()->format('Y-m-d');
        $detailUrl = route('calendar.events.show', ['academicEvent' => $event->slug]);

        $descriptionParts = array_filter([
            $event->description,
            'Detail Event: ' . $detailUrl,
        ]);

        return [
            'summary'     => $event->title,
            'description' => implode("\n\n", $descriptionParts),
            'location'    => $event->location,
            'start'       => ['date' => $startDate, 'timeZone' => 'Asia/Jakarta'],
            'end'         => ['date' => $endDate,   'timeZone' => 'Asia/Jakarta'],
            'source'      => [
                'title' => config('app.name') . ' - Event Akademik',
                'url'   => $detailUrl,
            ],
            'extendedProperties' => [
                'private' => [
                    'appAcademicEventId' => (string) $event->id,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findRemoteAcademicEvent(AcademicEvent $event): ?array
    {
        $response = $this->authorizedRequest()
            ->get($this->calendarBaseUrl() . '/events', [
                'privateExtendedProperty' => 'appAcademicEventId=' . $event->id,
                'maxResults'              => 1,
                'singleEvents'            => 'false',
                'showDeleted'             => 'false',
            ])
            ->throw()
            ->json();

        $items = $response['items'] ?? [];

        if (!is_array($items) || !isset($items[0]) || !is_array($items[0])) {
            return null;
        }

        return $items[0];
    }

    // =========================================================
    //  PRIVATE: Auth & HTTP Helpers
    // =========================================================

    private function calendarBaseUrl(): string
    {
        $calendarId = rawurlencode((string) config('services.google_calendar.calendar_id'));

        return 'https://www.googleapis.com/calendar/v3/calendars/' . $calendarId;
    }

    private function authorizedRequest(): \Illuminate\Http\Client\PendingRequest
    {
        $token = $this->getAccessToken();
        if ($token === null || $token === '') {
            throw new \RuntimeException('Google Calendar access token is unavailable.');
        }

        return Http::acceptJson()->withToken($token)->timeout(20);
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken !== null && time() < ($this->accessTokenExpiresAt - 60)) {
            return $this->accessToken;
        }

        $credentials = $this->readCredentials();
        if ($credentials === null) {
            return null;
        }

        $jwt = $this->createSignedJwt($credentials);

        $response = Http::asForm()
            ->timeout(20)
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ])
            ->throw()
            ->json();

        $token = (string) ($response['access_token'] ?? '');
        if ($token === '') {
            return null;
        }

        $this->accessToken          = $token;
        $this->accessTokenExpiresAt = time() + max((int) ($response['expires_in'] ?? 3600), 60);

        return $this->accessToken;
    }

    /**
     * @return array<string, string>|null
     */
    private function readCredentials(): ?array
    {
        $path = $this->credentialsPath();
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            return null;
        }

        foreach (['client_email', 'private_key'] as $key) {
            if (!isset($decoded[$key]) || !is_string($decoded[$key]) || $decoded[$key] === '') {
                return null;
            }
        }

        return [
            'client_email' => $decoded['client_email'],
            'private_key'  => $decoded['private_key'],
        ];
    }

    private function credentialsPath(): string
    {
        $configured = (string) config('services.google_calendar.service_account_json', '');
        if ($configured === '') {
            return '';
        }

        if (preg_match('/^(?:[A-Za-z]:\\\\|\/)/', $configured) === 1) {
            return $configured;
        }

        return base_path($configured);
    }

    /**
     * @param array<string, string> $credentials
     */
    private function createSignedJwt(array $credentials): string
    {
        $now = time();

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_THROW_ON_ERROR));

        $payload = $this->base64UrlEncode(json_encode([
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ], JSON_THROW_ON_ERROR));

        $unsignedToken = $header . '.' . $payload;
        $signature     = '';

        $signed = openssl_sign($unsignedToken, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        if ($signed !== true) {
            throw new \RuntimeException('Unable to sign Google JWT assertion.');
        }

        return $unsignedToken . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function isConfigured(): bool
    {
        $enabled    = (bool) config('services.google_calendar.enabled', false);
        $calendarId = (string) config('services.google_calendar.calendar_id', '');
        $path       = $this->credentialsPath();

        return $enabled && $calendarId !== '' && is_file($path);
    }
}
