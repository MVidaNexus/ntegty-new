<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            // إضافة عمود نوع الخط
            $table->string('font_family')->default('Cairo')->after('text_color');
            
            // تغيير الأعمدة لتقبل null مع قيم افتراضية
            $table->string('signature_left_text')->nullable()->default('مدير المدرسة')->change();
            $table->string('signature_right_text')->nullable()->default('الكادر الإداري')->change();
            $table->string('congratulations_text_male')->nullable()->change();
            $table->string('congratulations_text_female')->nullable()->change();
            $table->string('achievement_text_male')->nullable()->change();
            $table->string('achievement_text_female')->nullable()->change();
            $table->string('score_text_template')->nullable()->change();
            $table->string('wish_text')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $table->dropColumn('font_family');
        });
    }
};
