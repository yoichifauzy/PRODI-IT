<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old gallery_items table first (has foreign key to galleries)
        Schema::dropIfExists('gallery_items');
        // Drop and recreate galleries with new simpler schema
        Schema::dropIfExists('galleries');

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->index(); // e.g. 'stok-opname', 'wisuda'
            $table->integer('year')->index();    // e.g. 2026
            $table->string('image_path');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
