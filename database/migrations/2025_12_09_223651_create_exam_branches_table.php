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
        Schema::create('exam_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->string('name_ar'); // الاسم بالعربي (مثال: علمي علوم، تجاري)
            $table->string('name_en')->nullable(); // الاسم بالإنجليزي
            $table->string('code')->unique(); // كود فريد (مثال: eg_secondary_science, eg_diploma_commercial)
            $table->string('slug'); // للروابط
            $table->string('icon')->nullable(); // أيقونة Font Awesome
            $table->string('color')->default('blue'); // لون الشُعبة
            $table->decimal('total_score', 8, 2)->nullable(); // المجموع الكلي
            $table->decimal('passing_score', 8, 2)->nullable(); // درجة النجاح
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_branches');
    }
};
