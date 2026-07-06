<?php

use Illuminate\Database\Migrations\Migration;

// Migration ini dikosongkan karena kolom published_at dan sort_order
// tidak lagi digunakan di tabel activities dan gallery_items.
// Kolom tersebut sudah tidak ada di migration awal (create table).
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
