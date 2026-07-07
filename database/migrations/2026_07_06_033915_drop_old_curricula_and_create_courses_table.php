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
        Schema::dropIfExists('curriculum_courses');
        Schema::dropIfExists('curricula');

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->constrained('asset_links')->nullOnDelete();
            $table->string('semester', 50);
            $table->string('major_selection', 100)->nullable();
            $table->string('code', 50);
            $table->string('name', 255);
            $table->integer('credits_theory');
            $table->integer('credits_practice');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
