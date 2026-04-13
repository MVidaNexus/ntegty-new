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
        if (config('database.default') !== 'sqlite' && \Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('results', function (Blueprint $table) {
                $table->fullText('student_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') !== 'sqlite' && \Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('results', function (Blueprint $table) {
                $table->dropFullText(['student_name']);
            });
        }
    }
};
