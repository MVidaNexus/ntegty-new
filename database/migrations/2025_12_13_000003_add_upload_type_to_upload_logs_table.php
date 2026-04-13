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
        Schema::table('upload_logs', function (Blueprint $table) {
            // نوع الرفع: excel, pdf, embed, governorate_table, governorate_file
            $table->string('upload_type')->default('excel')->after('status');
            
            // بيانات إضافية للأنواع المختلفة (JSON)
            // تحتوي على: embed_url, embed_code, pdf_file, iframe_settings, governorate_files, etc.
            $table->json('extra_data')->nullable()->after('mapping_data');
            
            // ملاحظات أو وصف
            $table->text('notes')->nullable()->after('error_message');
            
            // Index للبحث السريع
            $table->index('upload_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropIndex(['upload_type']);
            $table->dropColumn(['upload_type', 'extra_data', 'notes']);
        });
    }
};
