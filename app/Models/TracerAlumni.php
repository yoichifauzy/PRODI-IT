<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TracerAlumni extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'document_id',
        'graduation_year',
        'nim',
        'name',
        'company_name',
        'department',
        'relevance',
        'contact',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'graduation_year' => 'integer'
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
