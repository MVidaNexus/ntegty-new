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
            $table->string('slug')->nullable()->after('code');
            $table->enum('level', ['preparatory', 'secondary'])->default('secondary')->after('slug');
            
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn(['slug', 'level']);
        });
    }
};
