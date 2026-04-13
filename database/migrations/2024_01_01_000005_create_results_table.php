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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->string('seat_number')->index();
            $table->string('student_name')->index();
            $table->foreignId('governorate_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->json('subjects_data'); // Flexible storage for all subjects and grades
            $table->decimal('total_score', 8, 2)->nullable();
            $table->string('status')->nullable(); // ناجح / راسب
            $table->timestamps();
            
            // Composite indexes for fast search
            $table->index(['governorate_id', 'exam_type_id', 'academic_year_id']);
            $table->index(['exam_type_id', 'academic_year_id', 'seat_number']);
            
            // Full-text index for Arabic name search
            // $table->fullText('student_name'); // Disabled for SQLite compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
