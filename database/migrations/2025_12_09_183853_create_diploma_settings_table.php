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
        Schema::create('diploma_settings', function (Blueprint $table) {
            $table->id();
            $table->string('country_code')->unique()->comment('كود الدولة مثل eg, iq, ly');
            $table->boolean('use_unified_service')->default(false)->comment('استخدام خدمة موحدة لكل الشعب');
            $table->enum('unified_service_type', ['search', 'embed', 'pdf'])->default('search')->comment('نوع الخدمة الموحدة');
            $table->text('unified_embed_code')->nullable()->comment('كود الإيفريم الموحد');
            $table->string('unified_pdf_path')->nullable()->comment('مسار ملف PDF الموحد');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diploma_settings');
    }
};
