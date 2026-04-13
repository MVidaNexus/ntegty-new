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
        Schema::table('certificate_settings', function (Blueprint $table) {
            // إزالة الأعمدة القديمة (النصوص الفردية)
            $table->dropColumn([
                'enabled_fields',
                'congratulations_text_male',
                'congratulations_text_female',
                'achievement_text_male',
                'achievement_text_female',
                'score_text_template',
                'wish_text',
                'text1_position_y',
                'text2_position_y',
                'exam_position_y',
                'score_position_y',
                'wish_position_y',
            ]);
            
            // إضافة الأعمدة الجديدة - 6 أسطر للذكور
            for ($i = 1; $i <= 6; $i++) {
                $table->text("line{$i}_text_male")->nullable();
                $table->string("line{$i}_font_family")->default('Cairo');
                $table->integer("line{$i}_font_size")->default(50);
                $table->string("line{$i}_color")->default('#374151');
                $table->integer("line{$i}_position_x")->default(1240); // center
                $table->integer("line{$i}_position_y")->default(800 + ($i * 100));
            }
            
            // 6 أسطر للإناث
            for ($i = 1; $i <= 6; $i++) {
                $table->text("line{$i}_text_female")->nullable();
            }
            
            // إعدادات التوقيع الأيسر
            $table->string('signature_left_font_family')->default('Cairo');
            $table->integer('signature_left_font_size')->default(45);
            $table->string('signature_left_color')->default('#1e3a8a');
            $table->integer('signature_left_position_x')->default(620);
            $table->integer('signature_left_position_y')->default(1500);
            
            // إعدادات التوقيع الأيمن
            $table->string('signature_right_font_family')->default('Cairo');
            $table->integer('signature_right_font_size')->default(45);
            $table->string('signature_right_color')->default('#1e3a8a');
            $table->integer('signature_right_position_x')->default(1860);
            $table->integer('signature_right_position_y')->default(1500);
            
            // موضع الاسم X
            $table->integer('name_position_x')->default(1240);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            // إزالة الأعمدة الجديدة
            for ($i = 1; $i <= 6; $i++) {
                $table->dropColumn([
                    "line{$i}_text_male",
                    "line{$i}_text_female",
                    "line{$i}_font_family",
                    "line{$i}_font_size",
                    "line{$i}_color",
                    "line{$i}_position_x",
                    "line{$i}_position_y",
                ]);
            }
            
            $table->dropColumn([
                'signature_left_font_family',
                'signature_left_font_size',
                'signature_left_color',
                'signature_left_position_x',
                'signature_left_position_y',
                'signature_right_font_family',
                'signature_right_font_size',
                'signature_right_color',
                'signature_right_position_x',
                'signature_right_position_y',
                'name_position_x',
            ]);
            
            // إعادة الأعمدة القديمة
            $table->json('enabled_fields')->nullable();
            $table->text('congratulations_text_male')->nullable();
            $table->text('congratulations_text_female')->nullable();
            $table->text('achievement_text_male')->nullable();
            $table->text('achievement_text_female')->nullable();
            $table->string('score_text_template')->nullable();
            $table->string('wish_text')->nullable();
            $table->integer('text1_position_y')->default(900);
            $table->integer('text2_position_y')->default(1000);
            $table->integer('exam_position_y')->default(1120);
            $table->integer('score_position_y')->default(1230);
            $table->integer('wish_position_y')->default(1330);
        });
    }
};
