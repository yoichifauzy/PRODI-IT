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
        if (!Schema::hasColumn('hero_slides', 'created_by')) {
            Schema::table('hero_slides', function (Blueprint $table): void {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_at');
            });
        }

        if (!Schema::hasColumn('lecturer_staff', 'created_by')) {
            Schema::table('lecturer_staff', function (Blueprint $table): void {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('hero_slides', 'created_by')) {
            Schema::table('hero_slides', function (Blueprint $table): void {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }

        if (Schema::hasColumn('lecturer_staff', 'created_by')) {
            Schema::table('lecturer_staff', function (Blueprint $table): void {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }
    }
};
