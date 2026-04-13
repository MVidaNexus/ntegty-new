<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('default'); // اسم القالب
            $table->boolean('is_active')->default(true);
            
            // إعدادات الصفحة
            $table->string('page_title')->default('تصميم شهادة تقدير للمتفوقين');
            $table->text('page_description')->nullable();
            
            // صورة الخلفية
            $table->string('background_image')->nullable();
            $table->integer('canvas_width')->default(2480);
            $table->integer('canvas_height')->default(1754);
            
            // الخانات المطلوبة (JSON array)
            $table->json('enabled_fields')->nullable(); // ['student_name', 'school_name', 'exam_type', 'score', 'gender']
            
            // نصوص الشهادة
            $table->string('congratulations_text_male')->default('تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات');
            $table->string('congratulations_text_female')->default('تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات');
            $table->string('achievement_text_male')->default('للطالب المتفوق تقديراً لجهوده المبذولة وتميزه الدراسي');
            $table->string('achievement_text_female')->default('للطالبة المتفوقة تقديراً لجهودها المبذولة وتميزها الدراسي');
            $table->string('score_text_template')->default('وذلك لحصوله على المركز المتميز في {exam_type}');
            $table->string('wish_text')->default('متمنين له دوام التوفيق والنجاح في مسيرته العلمية');
            
            // إعدادات الموضع (بالبيكسل)
            $table->integer('name_position_y')->default(700);
            $table->integer('text1_position_y')->default(900);
            $table->integer('text2_position_y')->default(1000);
            $table->integer('exam_position_y')->default(1120);
            $table->integer('score_position_y')->default(1230);
            $table->integer('wish_position_y')->default(1330);
            
            // إعدادات الخطوط
            $table->integer('name_font_size')->default(110);
            $table->integer('text_font_size')->default(60);
            $table->integer('small_text_font_size')->default(50);
            $table->string('primary_color')->default('#1e3a8a'); // أزرق
            $table->string('secondary_color')->default('#c2410c'); // برتقالي
            $table->string('text_color')->default('#374151'); // رمادي غامق
            
            // إعدادات التوقيع
            $table->string('signature_left_text')->default('مدير المدرسة');
            $table->string('signature_right_text')->default('الكادر الإداري');
            $table->boolean('show_date')->default(true);
            
            $table->timestamps();
        });
        
        // إدراج القيم الافتراضية
        \DB::table('certificate_settings')->insert([
            'name' => 'default',
            'is_active' => true,
            'page_title' => 'تصميم شهادة تقدير للمتفوقين',
            'page_description' => 'اصنع ذكرى جميلة لنجاحك واحتفظ بشهادة تقدير بتصميم احترافي في ثوانٍ ✨',
            'enabled_fields' => json_encode(['student_name', 'school_name', 'exam_type', 'total_score', 'max_score', 'gender']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_settings');
    }
};
