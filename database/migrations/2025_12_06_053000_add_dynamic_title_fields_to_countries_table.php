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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('government_type')->default('جمهورية')->after('name_en'); // جمهورية، مملكة، إمارة، دولة
            $table->string('academic_year')->default('2026')->after('government_type'); // 2026, 2025-2026
            $table->string('semester')->nullable()->after('academic_year'); // الفصل الدراسي الأول، الثاني، null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['government_type', 'academic_year', 'semester']);
        });
    }
};
