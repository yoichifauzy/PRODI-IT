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
        Schema::dropIfExists('tracer_alumnis');

        Schema::create('tracer_alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->constrained('asset_links')->nullOnDelete();
            $table->integer('graduation_year');
            $table->string('nim', 50);
            $table->string('company_name');
            $table->string('department');
            $table->string('relevance');
            $table->string('contact')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_alumnis');
    }
};
