<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'description_primary',
        'description_secondary',
        'vision_text',
        'mission_text',
        'image_one_path',
        'image_two_path',
        'video_path',
    ];

    protected $casts = [
        'mission_text' => 'array',
    ];
}
