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
        Schema::table('results', function (Blueprint $table) {
            // Composite index for governorate queries with ranking
            $table->index(['exam_type_id', 'academic_year_id', 'governorate_id', 'total_score'], 'idx_results_gov_ranking');
            
            // Composite index for country-wide queries (secondary exams)
            $table->index(['exam_type_id', 'academic_year_id', 'branch_id', 'total_score'], 'idx_results_country_ranking');
            
            // Index for total_score alone (used in many comparisons)
            $table->index('total_score', 'idx_results_total_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex('idx_results_gov_ranking');
            $table->dropIndex('idx_results_country_ranking');
            $table->dropIndex('idx_results_total_score');
        });
    }
};
