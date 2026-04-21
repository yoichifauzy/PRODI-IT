<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerStaff extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const TYPES = ['lecturer', 'staff'];

    protected $table = 'lecturer_staff';

    protected $fillable = [
        'name',
        'position',
        'type',
        'email',
        'bio',
        'photo_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function blogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LecturerStaffBlog::class);
    }
}
