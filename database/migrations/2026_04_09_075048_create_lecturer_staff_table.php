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
        Schema::create('lecturer_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->enum('type', ['lecturer', 'staff'])->default('lecturer');
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_staff');
    }
};
