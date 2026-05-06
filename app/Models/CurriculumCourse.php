<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourse extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'curriculum_id',
        'code',
        'name',
        'credits_theory',
        'credits_practice',
        'credits',
        'short_syllabus',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'credits_theory' => 'integer',
            'credits_practice' => 'integer',
            'credits' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // Backward-compatible attribute used by existing forms/tables.
    public function getCreditsTheoryAttribute(): int
    {
        return (int) ($this->attributes['credits_theory'] ?? $this->attributes['credits'] ?? 0);
    }

    // Backward-compatible attribute used by existing forms/tables.
    public function getCreditsPracticeAttribute(): int
    {
        return (int) ($this->attributes['credits_practice'] ?? 0);
    }

    public function curriculum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
