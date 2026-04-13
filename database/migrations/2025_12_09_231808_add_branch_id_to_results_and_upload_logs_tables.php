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
        // Add branch_id to results table
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('exam_type_id')
                  ->constrained('exam_branches')->nullOnDelete();
            $table->index('branch_id');
        });
        
        // Add branch_id to upload_logs table
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('exam_type_id')
                  ->constrained('exam_branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['branch_id']);
            $table->dropColumn('branch_id');
        });
        
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
