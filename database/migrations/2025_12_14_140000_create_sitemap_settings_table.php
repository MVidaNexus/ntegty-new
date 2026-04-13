<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sitemap_settings', function (Blueprint $table) {
            $table->id();
            
            // الإعدادات الأساسية
            $table->boolean('is_enabled')->default(true);
            $table->boolean('auto_generate')->default(true);
            $table->integer('urls_per_sitemap')->default(5000); // عدد الروابط في كل خريطة
            $table->integer('cache_hours')->default(6); // مدة الكاش بالساعات
            
            // تفعيل/تعطيل أقسام الخريطة
            $table->boolean('include_pages')->default(true);
            $table->boolean('include_countries')->default(true);
            $table->boolean('include_exam_types')->default(true);
            $table->boolean('include_governorates')->default(true);
            $table->boolean('include_branches')->default(true);
            $table->boolean('include_students')->default(true);
            $table->boolean('include_schools')->default(true);
            $table->boolean('include_administrations')->default(true);
            $table->boolean('include_top_students')->default(true);
            
            // إعدادات الأولوية
            $table->decimal('priority_home', 2, 1)->default(1.0);
            $table->decimal('priority_countries', 2, 1)->default(0.9);
            $table->decimal('priority_exam_types', 2, 1)->default(0.85);
            $table->decimal('priority_governorates', 2, 1)->default(0.8);
            $table->decimal('priority_students', 2, 1)->default(0.7);
            $table->decimal('priority_schools', 2, 1)->default(0.6);
            
            // تردد التحديث
            $table->string('changefreq_home')->default('daily');
            $table->string('changefreq_countries')->default('daily');
            $table->string('changefreq_students')->default('weekly');
            
            // إحصائيات (يتم تحديثها تلقائياً)
            $table->integer('total_urls')->default(0);
            $table->integer('total_sitemaps')->default(0);
            $table->json('sitemaps_stats')->nullable(); // إحصائيات تفصيلية لكل خريطة
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamp('last_submitted_at')->nullable();
            
            // روابط إضافية مخصصة
            $table->json('custom_urls')->nullable();
            
            // روابط مستبعدة
            $table->json('excluded_patterns')->nullable();
            
            $table->timestamps();
        });
        
        // إنشاء جدول لسجل توليد الخرائط
        Schema::create('sitemap_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sitemap_name');
            $table->string('sitemap_type'); // index, pages, students, etc.
            $table->integer('urls_count');
            $table->integer('file_size')->nullable();
            $table->decimal('generation_time', 8, 2)->nullable(); // بالثواني
            $table->enum('status', ['success', 'failed', 'cached'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });
        
        // إدراج الإعدادات الافتراضية
        \DB::table('sitemap_settings')->insert([
            'is_enabled' => true,
            'auto_generate' => true,
            'urls_per_sitemap' => 5000,
            'cache_hours' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sitemap_logs');
        Schema::dropIfExists('sitemap_settings');
    }
};
