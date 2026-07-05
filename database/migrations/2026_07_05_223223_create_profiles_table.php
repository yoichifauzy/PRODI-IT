<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->text('description_primary')->nullable();
            $table->text('description_secondary')->nullable();
            $table->text('vision_text')->nullable();
            $table->text('mission_text')->nullable();
            $table->string('image_one_path')->nullable();
            $table->string('image_two_path')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();
        });

        // Migrate data
        $settings = DB::table('settings')
            ->whereIn('key', [
                'about_description_primary',
                'about_description_secondary',
                'about_image_one',
                'about_image_two',
                'about_video_path',
            ])
            ->pluck('value', 'key');

        $visionMission = DB::table('vision_missions')->where('is_active', true)->first();

        DB::table('profiles')->insert([
            'description_primary' => $settings['about_description_primary'] ?? null,
            'description_secondary' => $settings['about_description_secondary'] ?? null,
            'vision_text' => $visionMission?->vision_text ?? null,
            'mission_text' => $visionMission?->mission_text ?? null,
            'image_one_path' => $settings['about_image_one'] ?? null,
            'image_two_path' => $settings['about_image_two'] ?? null,
            'video_path' => $settings['about_video_path'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
