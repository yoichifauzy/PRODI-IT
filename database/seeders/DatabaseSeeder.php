<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@prodi-it.local'),
        ], [
            'name' => 'Admin Prodi TI',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin12345')),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
