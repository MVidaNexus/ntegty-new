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
            // نوع خدمة النتائج: search (بحث عادي) أو embed (iframe) أو pdf
            $table->string('result_service_type')->default('search')->after('result_pdf_path');
            
            // إعدادات الـ embed/iframe
            $table->text('embed_code')->nullable()->after('result_service_type');
            $table->string('iframe_url')->nullable()->after('embed_code');
            $table->string('iframe_width')->default('100%')->after('iframe_url');
            $table->string('iframe_height')->default('600px')->after('iframe_width');
            $table->string('iframe_position')->default('center')->after('iframe_height');
            $table->boolean('iframe_scrolling')->default(true)->after('iframe_position');
            $table->boolean('iframe_border')->default(false)->after('iframe_scrolling');
            $table->boolean('iframe_crop_enabled')->default(false)->after('iframe_border');
            $table->integer('iframe_crop_top')->default(0)->after('iframe_crop_enabled');
            $table->integer('iframe_crop_left')->default(0)->after('iframe_crop_top');
            $table->decimal('iframe_zoom', 3, 2)->default(1.00)->after('iframe_crop_left');
            
            // إعدادات PDF
            $table->string('pdf_file_path')->nullable()->after('iframe_zoom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn([
                'result_service_type',
                'embed_code',
                'iframe_url',
                'iframe_width',
                'iframe_height',
                'iframe_position',
                'iframe_scrolling',
                'iframe_border',
                'iframe_crop_enabled',
                'iframe_crop_top',
                'iframe_crop_left',
                'iframe_zoom',
                'pdf_file_path',
            ]);
        });
    }
};
