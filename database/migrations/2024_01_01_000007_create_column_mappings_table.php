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
        Schema::create('column_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->string('mapping_name'); // e.g., "Cairo Preparatory 2024"
            $table->json('column_map'); // {"seat_number": "رقم الجلوس", "student_name": "اسم الطالب", ...}
            $table->timestamps();
            
            $table->index('exam_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('column_mappings');
    }
};
