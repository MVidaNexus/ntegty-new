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
            $table->string('system_type')->nullable()->after('exam_type_id')->comment('old, new, or null for general');
        });

        Schema::table('upload_logs', function (Blueprint $table) {
            $table->string('system_type')->nullable()->after('exam_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('system_type');
        });

        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropColumn('system_type');
        });
    }
};
