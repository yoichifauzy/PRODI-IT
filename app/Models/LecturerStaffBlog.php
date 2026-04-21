<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerStaffBlog extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'lecturer_staff_id',
        'title',
        'slug',
        'description',
        'location',
        'activity_date',
        'cover_image',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function lecturerStaff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LecturerStaff::class);
    }
}
