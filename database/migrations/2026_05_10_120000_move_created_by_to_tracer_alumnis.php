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
        // Remove created_by from tracer_study_links if present
        if (Schema::hasColumn('tracer_study_links', 'created_by')) {
            Schema::table('tracer_study_links', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }

        // Add created_by to tracer_alumnis
        if (!Schema::hasColumn('tracer_alumnis', 'created_by')) {
            Schema::table('tracer_alumnis', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove created_by from tracer_alumnis
        if (Schema::hasColumn('tracer_alumnis', 'created_by')) {
            Schema::table('tracer_alumnis', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }

        // Add created_by back to tracer_study_links
        if (!Schema::hasColumn('tracer_study_links', 'created_by')) {
            Schema::table('tracer_study_links', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('published_at');
            });
        }
    }
};
