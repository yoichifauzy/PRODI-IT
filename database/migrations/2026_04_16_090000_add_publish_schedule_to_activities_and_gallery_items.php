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
        Schema::table('activities', function (Blueprint $table): void {
            if (!Schema::hasColumn('activities', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('event_date');
                $table->index('published_at', 'activities_published_at_idx');
            }
        });

        Schema::table('gallery_items', function (Blueprint $table): void {
            if (!Schema::hasColumn('gallery_items', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('taken_at');
                $table->index('published_at', 'gallery_items_published_at_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table): void {
            if (Schema::hasColumn('activities', 'published_at')) {
                $table->dropIndex('activities_published_at_idx');
                $table->dropColumn('published_at');
            }
        });

        Schema::table('gallery_items', function (Blueprint $table): void {
            if (Schema::hasColumn('gallery_items', 'published_at')) {
                $table->dropIndex('gallery_items_published_at_idx');
                $table->dropColumn('published_at');
            }
        });
    }
};
