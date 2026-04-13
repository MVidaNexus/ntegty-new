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
            // نوع خدمة عرض النتيجة: search (افتراضي), embed, pdf
            $table->enum('result_service_type', ['search', 'embed', 'pdf'])->default('search')->after('excluded_subjects');
            
            // رابط أو كود الإيميد (iframe)
            $table->text('embed_code')->nullable()->after('result_service_type');
            
            // مسار ملف PDF
            $table->string('pdf_file_path')->nullable()->after('embed_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['result_service_type', 'embed_code', 'pdf_file_path']);
        });
    }
};
