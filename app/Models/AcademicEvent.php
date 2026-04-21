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
}
