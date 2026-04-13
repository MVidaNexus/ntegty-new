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
            // إعدادات الترم الأول
            $table->decimal('semester1_total_score', 10, 2)->nullable()->after('second_round_threshold')
                ->comment('المجموع الكلي للترم الأول');
            $table->decimal('semester1_passing_score', 10, 2)->nullable()->after('semester1_total_score')
                ->comment('حد النجاح للترم الأول');
            $table->decimal('semester1_second_round', 10, 2)->nullable()->after('semester1_passing_score')
                ->comment('حد الدور الثاني للترم الأول');
            
            // إعدادات الترم الثاني
            $table->decimal('semester2_total_score', 10, 2)->nullable()->after('semester1_second_round')
                ->comment('المجموع الكلي للترم الثاني');
            $table->decimal('semester2_passing_score', 10, 2)->nullable()->after('semester2_total_score')
                ->comment('حد النجاح للترم الثاني');
            $table->decimal('semester2_second_round', 10, 2)->nullable()->after('semester2_passing_score')
                ->comment('حد الدور الثاني للترم الثاني');
            
            // تفعيل نظام الفصول الدراسية
            $table->boolean('has_semester_settings')->default(false)->after('semester2_second_round')
                ->comment('تفعيل نظام الفصول الدراسية المنفصلة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn([
                'semester1_total_score',
                'semester1_passing_score',
                'semester1_second_round',
                'semester2_total_score',
                'semester2_passing_score',
                'semester2_second_round',
                'has_semester_settings',
            ]);
        });
    }
};
