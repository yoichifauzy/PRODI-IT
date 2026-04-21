<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('lecturer_staff_blogs')) {
            $existingIndexNames = array_map(
                static fn($row) => (string) $row->Key_name,
                DB::select('SHOW INDEX FROM lecturer_staff_blogs')
            );

            if (!in_array('lecturer_staff_blogs_slug_unique', $existingIndexNames, true)) {
                Schema::table('lecturer_staff_blogs', function (Blueprint $table): void {
                    $table->unique('slug');
                });
            }

            if (!in_array('lsb_member_pub_date_idx', $existingIndexNames, true)) {
                Schema::table('lecturer_staff_blogs', function (Blueprint $table): void {
                    $table->index(['lecturer_staff_id', 'is_published', 'activity_date'], 'lsb_member_pub_date_idx');
                });
            }

            return;
        }

        Schema::create('lecturer_staff_blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_staff_id')->constrained('lecturer_staff')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('activity_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['lecturer_staff_id', 'is_published', 'activity_date'], 'lsb_member_pub_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_staff_blogs');
    }
};
