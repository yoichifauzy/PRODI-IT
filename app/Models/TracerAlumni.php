<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TracerAlumni extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nim',
        'graduation_year',
        'company_name',
        'company_level',
        'department',
        'relevance',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'graduation_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
