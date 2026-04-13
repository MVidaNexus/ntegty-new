<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة إعدادات قص/تحديد منطقة الـ iframe
     */
    public function up(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            // إعدادات القص (Cropping)
            $table->boolean('iframe_crop_enabled')->default(false)->after('iframe_border');
            $table->string('iframe_crop_top')->nullable()->after('iframe_crop_enabled')->default('0');
            $table->string('iframe_crop_left')->nullable()->after('iframe_crop_top')->default('0');
            $table->string('iframe_zoom')->nullable()->after('iframe_crop_left')->default('100');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['iframe_crop_enabled', 'iframe_crop_top', 'iframe_crop_left', 'iframe_zoom']);
        });
    }
};
