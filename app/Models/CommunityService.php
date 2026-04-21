<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityService extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'activity_date',
        'location',
        'organizer',
        'summary',
        'documentation_cover',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
