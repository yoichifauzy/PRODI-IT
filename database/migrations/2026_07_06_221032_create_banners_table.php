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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            
            // Diberi index() karena kita akan sering melakukan query pencarian berdasarkan kategori (where('category', '...'))
            $table->string('category')->index(); 
            
            $table->string('image_path');
            $table->integer('position')->default(0); // Urutan/Sort order
            
            // created_by diset unsignedBigInteger agar bisa direlasikan dengan tabel users (jika diperlukan)
            $table->unsignedBigInteger('created_by')->nullable(); 
            
            // timestamps() otomatis membuat created_at dan updated_at
            $table->timestamps(); 

            // Opsional: Jika kamu ingin menambahkan foreign key ke tabel users
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};