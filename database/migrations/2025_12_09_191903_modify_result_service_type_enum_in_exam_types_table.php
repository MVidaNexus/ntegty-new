<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل enum لإضافة governorate_table
        if (config('database.default') !== 'sqlite' && \Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE exam_types MODIFY COLUMN result_service_type ENUM('search', 'embed', 'pdf', 'governorate_table') DEFAULT 'search'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع القيم القديمة
        if (config('database.default') !== 'sqlite' && \Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("UPDATE exam_types SET result_service_type = 'search' WHERE result_service_type = 'governorate_table'");
            DB::statement("ALTER TABLE exam_types MODIFY COLUMN result_service_type ENUM('search', 'embed', 'pdf') DEFAULT 'search'");
        }
    }
};
