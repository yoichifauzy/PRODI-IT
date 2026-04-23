<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicEvent extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const EVENT_TYPES = ['krs', 'uts', 'uas', 'holiday', 'seminar', 'other'];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'location',
        'google_event_url',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_published', true);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function googleCalendarUrl(): string
    {
        if (!empty($this->google_event_url)) {
            return (string) $this->google_event_url;
        }

        if ($this->start_date === null) {
            return '';
        }

        $startDate = $this->start_date->format('Ymd');
        $endSource = $this->end_date ?? $this->start_date;
        $endDate = $endSource->copy()->addDay()->format('Ymd');

        $params = [
            'action' => 'TEMPLATE',
            'text' => (string) ($this->title ?? ''),
            'dates' => $startDate . '/' . $endDate,
            'details' => (string) ($this->description ?? ''),
            'location' => (string) ($this->location ?? ''),
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
