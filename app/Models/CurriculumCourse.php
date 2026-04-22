<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourse extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'curriculum_id',
        'major_selection',
        // 'semester',
        'code',
        'name',
        'credits_theory',
        'credits_practice',
        // 'short_syllabus',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            // 'semester' => 'integer',
            'credits_theory' => 'integer',
            'credits_practice' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function curriculum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Curriculum::class)->orderBy('code');
    }
}
