<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerAlumniSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'is_active',
        'order',
    ];
}
