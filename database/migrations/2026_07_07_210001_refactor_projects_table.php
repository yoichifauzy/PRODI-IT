<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop legacy columns from projects table.
     * (thumbnail -> image_path sudah ada di DB,
     *  summary -> description sudah ada,
     *  is_featured -> is_feature sudah ada)
     * Hanya drop: slug, status, published_at jika masih ada.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = Schema::getColumnListing('projects');

            if (in_array('slug', $columns))         $table->dropColumn('slug');
            if (in_array('status', $columns))       $table->dropColumn('status');
            if (in_array('published_at', $columns)) $table->dropColumn('published_at');
            if (in_array('thumbnail', $columns))    $table->renameColumn('thumbnail', 'image_path');
            if (in_array('summary', $columns))      $table->renameColumn('summary', 'description');
            if (in_array('is_featured', $columns))  $table->renameColumn('is_featured', 'is_feature');
        });
    }

    public function down(): void
    {
        // No-op for safety
    }
};
