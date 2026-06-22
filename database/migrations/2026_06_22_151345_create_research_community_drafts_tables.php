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
        Schema::create('researches_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('researcher_name');
            $table->enum('researcher_role', ['dosen', 'mahasiswa', 'kolaborasi'])->default('dosen');
            $table->smallInteger('year')->unsigned();
            $table->string('publication')->nullable();
            $table->string('link')->nullable();
            $table->text('abstract')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('community_services_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('activity_date');
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->text('summary')->nullable();
            $table->string('documentation_cover')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_services_drafts');
        Schema::dropIfExists('researches_drafts');
    }
};
