<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportSheet extends Model
{
    protected $fillable = ['name', 'type', 'file_path', 'url', 'is_active','created_timestamp'];
}