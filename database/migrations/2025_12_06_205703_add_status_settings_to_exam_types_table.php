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
            $table->decimal('total_score', 8, 2)->nullable()->after('level')->comment('المجموع الكلي للامتحان');
            $table->decimal('passing_score', 8, 2)->nullable()->after('total_score')->comment('حد النجاح (50%)');
            $table->decimal('second_round_threshold', 8, 2)->nullable()->after('passing_score')->comment('حد الدور الثاني');
            $table->boolean('auto_calculate_status')->default(true)->after('second_round_threshold')->comment('حساب الحالة تلقائياً');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['total_score', 'passing_score', 'second_round_threshold', 'auto_calculate_status']);
        });
    }
};
