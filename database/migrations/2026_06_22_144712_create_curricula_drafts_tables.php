<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curricula_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('curriculum_course_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_draft_id')->constrained('curricula_drafts')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('credits_theory')->default(0);
            $table->tinyInteger('credits_practice')->default(0);
            $table->tinyInteger('credits')->default(0);
            $table->text('short_syllabus')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_course_drafts');
        Schema::dropIfExists('curricula_drafts');
    }
};
