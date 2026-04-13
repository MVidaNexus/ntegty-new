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
            // semester: 1 = الفصل الدراسي الأول, 2 = الفصل الدراسي الثاني, 0 = الترمين (مجمع)
            $table->tinyInteger('semester')->default(0)->after('system_type')->index();
        });
        
        // Also add semester to upload_logs for tracking
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->tinyInteger('semester')->default(0)->after('system_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
        
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
