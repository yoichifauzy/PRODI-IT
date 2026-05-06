<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $needsTheory = !Schema::hasColumn('curriculum_courses', 'credits_theory');
        $needsPractice = !Schema::hasColumn('curriculum_courses', 'credits_practice');

        if ($needsTheory || $needsPractice) {
            Schema::table('curriculum_courses', function (Blueprint $table) use ($needsTheory, $needsPractice) {
                if ($needsTheory) {
                    $table->unsignedTinyInteger('credits_theory')->default(0)->after('name');
                }
                if ($needsPractice) {
                    $table->unsignedTinyInteger('credits_practice')->default(0)->after($needsTheory ? 'credits_theory' : 'name');
                }
            });
        }

        if (($needsTheory || $needsPractice) && Schema::hasColumn('curriculum_courses', 'credits')) {
            DB::table('curriculum_courses')->update([
                'credits_theory' => DB::raw('COALESCE(credits, 0)'),
                'credits_practice' => DB::raw('COALESCE(credits_practice, 0)')
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('curriculum_courses', 'credits_theory') || Schema::hasColumn('curriculum_courses', 'credits_practice')) {
            Schema::table('curriculum_courses', function (Blueprint $table) {
                if (Schema::hasColumn('curriculum_courses', 'credits_theory')) {
                    $table->dropColumn('credits_theory');
                }
                if (Schema::hasColumn('curriculum_courses', 'credits_practice')) {
                    $table->dropColumn('credits_practice');
                }
            });
        }
    }
};
