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
        Schema::create('learning_outcome', function (Blueprint $table) {
            $table->id();
            
            // Diberi index() karena kita akan sering melakukan query pencarian berdasarkan kategori (where('category', '...'))
            $table->string('code')->index(); 
            
            $table->text('description');
            
            // created_by diset unsignedBigInteger agar bisa direlasikan dengan tabel users (jika diperlukan)
            $table->unsignedBigInteger('created_by')->nullable(); 
            
            // timestamps() otomatis membuat created_at dan updated_at
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_outcome');
    }
};
