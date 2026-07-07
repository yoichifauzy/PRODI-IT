<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'title',
        'student_name',
        'student_nim',
        'year',
        'description',
        'image_path',
        'is_feature',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year'       => 'integer',
            'is_feature' => 'boolean',
        ];
    }

    /**
     * Scope: only featured projects.
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_feature', true);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
