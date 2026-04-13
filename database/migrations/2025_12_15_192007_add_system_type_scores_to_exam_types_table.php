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
        Schema::table('exam_types', function (Blueprint $table) {
            // إعدادات نظام الدراسة (للثانوية العامة)
            $table->boolean('has_system_type_settings')->default(false)->after('has_semester_settings');
            
            // النظام القديم
            $table->decimal('old_system_total_score', 8, 2)->nullable()->after('has_system_type_settings');
            $table->decimal('old_system_passing_score', 8, 2)->nullable()->after('old_system_total_score');
            $table->decimal('old_system_second_round', 8, 2)->nullable()->after('old_system_passing_score');
            
            // النظام الحديث
            $table->decimal('new_system_total_score', 8, 2)->nullable()->after('old_system_second_round');
            $table->decimal('new_system_passing_score', 8, 2)->nullable()->after('new_system_total_score');
            $table->decimal('new_system_second_round', 8, 2)->nullable()->after('new_system_passing_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn([
                'has_system_type_settings',
                'old_system_total_score',
                'old_system_passing_score',
                'old_system_second_round',
                'new_system_total_score',
                'new_system_passing_score',
                'new_system_second_round',
            ]);
        });
    }
};
