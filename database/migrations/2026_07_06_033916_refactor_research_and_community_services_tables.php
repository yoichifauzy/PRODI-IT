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
        Schema::dropIfExists('community_services');
        Schema::dropIfExists('researches');

        Schema::create('community_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->constrained('asset_links')->nullOnDelete();
            $table->string('title');
            $table->string('location')->nullable();
            $table->integer('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->constrained('asset_links')->nullOnDelete();
            $table->string('title');
            $table->string('researcher_name');
            $table->integer('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
        Schema::dropIfExists('community_services');
    }
};
