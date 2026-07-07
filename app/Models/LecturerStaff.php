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
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer'
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
