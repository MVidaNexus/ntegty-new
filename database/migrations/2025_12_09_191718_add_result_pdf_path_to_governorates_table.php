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
        Schema::table('governorates', function (Blueprint $table) {
            $table->string('result_pdf_path')->nullable()->after('logo_path')->comment('مسار ملف نتيجة المحافظة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('result_pdf_path');
        });
    }
};
