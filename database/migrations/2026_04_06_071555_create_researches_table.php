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
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('researcher_name');
            $table->enum('researcher_role', ['dosen', 'mahasiswa', 'kolaborasi'])->default('dosen');
            $table->unsignedSmallInteger('year');
            $table->string('publication')->nullable();
            $table->string('link')->nullable();
            $table->text('abstract')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['year', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
