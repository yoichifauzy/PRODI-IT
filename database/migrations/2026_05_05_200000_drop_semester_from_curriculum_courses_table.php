<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('curriculum_courses', 'semester')) {
            return;
        }

        Schema::table('curriculum_courses', function (Blueprint $table) {
            if (DB::getDriverName() === 'sqlite') {
                $table->dropUnique('curriculum_courses_curriculum_id_semester_code_unique');
            }
            $table->dropIndex('curriculum_courses_curriculum_id_semester_index');
            $table->dropColumn('semester');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('curriculum_courses', 'semester')) {
            return;
        }

        Schema::table('curriculum_courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->default(1)->after('curriculum_id');
            $table->unique(['curriculum_id', 'semester', 'code']);
            $table->index(['curriculum_id', 'semester']);
        });
    }
};
