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
        Schema::table('curricula', function (Blueprint $table): void {
            if (Schema::hasColumn('curricula', 'academic_year')) {
                $table->dropColumn('academic_year');
            }

            if (Schema::hasColumn('curricula', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table): void {
            if (!Schema::hasColumn('curricula', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('name');
            }

            if (!Schema::hasColumn('curricula', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('description');
            }
        });
    }
};
