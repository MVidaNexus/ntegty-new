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
            $table->boolean('is_result_approved')->default(false)->after('show_popular_searches');
            $table->text('result_announcement_text')->nullable()->after('is_result_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['is_result_approved', 'result_announcement_text']);
        });
    }
};
