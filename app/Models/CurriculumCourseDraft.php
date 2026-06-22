<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourseDraft extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'curriculum_course_drafts';

    protected $fillable = [
        'curriculum_draft_id',
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

    public function getCreditsTheoryAttribute(): int
    {
        return (int) ($this->attributes['credits_theory'] ?? $this->attributes['credits'] ?? 0);
    }

    public function getCreditsPracticeAttribute(): int
    {
        return (int) ($this->attributes['credits_practice'] ?? 0);
    }

    public function curriculum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CurriculumDraft::class, 'curriculum_draft_id');
    }
}
