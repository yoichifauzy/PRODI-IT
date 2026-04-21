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
        Schema::table('tracer_alumnis', function (Blueprint $table): void {
            if (!Schema::hasColumn('tracer_alumnis', 'graduation_year')) {
                $table->unsignedSmallInteger('graduation_year')->nullable()->after('nim');
                $table->index('graduation_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracer_alumnis', function (Blueprint $table): void {
            if (Schema::hasColumn('tracer_alumnis', 'graduation_year')) {
                $table->dropIndex(['graduation_year']);
                $table->dropColumn('graduation_year');
            }
        });
    }
};
