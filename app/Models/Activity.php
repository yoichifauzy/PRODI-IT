<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'category',
        'title',
        'description',
        'location',
        'event_date',
        'start_at',
        'end_at',
        'image_path',
        'google_event_url',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'start_at'   => 'datetime',
            'end_at'     => 'datetime',
        ];
    }

    /**
     * Scope: hanya kegiatan yang tampil di publik (is_published = true).
     */
    public function scopeVisibleOnPublic(Builder $query): Builder
    {
        return $query; // Semua kegiatan langsung tampil
    }

    /**
     * Scope: kegiatan yang layak tampil sebagai Running Card.
     * Kondisi: event_date antara (hari ini - 3 hari) s/d masa depan.
     * Artinya: kegiatan upcoming dan yang sudah lewat maksimal H+3 masih tampil.
     */
    public function scopeUpcomingRunningCard(Builder $query): Builder
    {
        $cutoff = Carbon::today()->subDays(3);

        return $query
            ->where('event_date', '>=', $cutoff)
            ->orderBy('event_date');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Kembalikan URL Google Calendar.
     * Prioritas 1: link dari sync otomatis (google_event_url).
     * Prioritas 2: generate URL template manual (fallback).
     */
    public function googleCalendarUrl(): string
    {
        if (!empty($this->google_event_url)) {
            return (string) $this->google_event_url;
        }

        if ($this->event_date === null) {
            return '';
        }

        // Gunakan dateTime jika ada start_at, fallback ke date-only
        if ($this->start_at !== null) {
            $startStr = $this->start_at->format('Ymd\THis');
            $endStr   = ($this->end_at ?? $this->start_at->copy()->addHours(2))->format('Ymd\THis');
        } else {
            $startStr = $this->event_date->format('Ymd');
            $endStr   = $this->event_date->copy()->addDay()->format('Ymd');
        }

        $params = [
            'action'   => 'TEMPLATE',
            'text'     => (string) ($this->title ?? ''),
            'dates'    => $startStr . '/' . $endStr,
            'details'  => (string) ($this->description ?? ''),
            'location' => (string) ($this->location ?? ''),
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
