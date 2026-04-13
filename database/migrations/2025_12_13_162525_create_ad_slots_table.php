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
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المكان (للعرض في الأدمن)
            $table->string('slug')->unique(); // معرف فريد للمكان
            $table->string('page_type'); // home, country, governorate, result, global
            $table->string('position'); // header_top, header_bottom, before_title, after_title, before_search, inside_search, after_search, sidebar, footer, sticky_bottom, between_results
            $table->string('ad_format')->default('auto'); // auto, display, in-article, in-feed, multiplex
            $table->string('ad_layout')->nullable(); // للإعلانات المتقدمة
            $table->string('slot_id')->nullable(); // data-ad-slot (اختياري)
            $table->string('custom_channel')->nullable(); // القناة المخصصة
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);
            $table->string('custom_style')->nullable(); // CSS مخصص
            $table->text('custom_code')->nullable(); // كود إعلان مخصص (بديل للـ AdSense)
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['page_type', 'position', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};
