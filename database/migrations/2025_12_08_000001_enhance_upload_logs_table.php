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
        Schema::table('upload_logs', function (Blueprint $table) {
            // Metadata for the upload batch
            $table->string('batch_name')->nullable()->after('filename')->comment('Friendly name for this upload batch');
            $table->string('file_path')->nullable()->after('filename');
            
            // Context (Where does this result belong?)
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->foreignId('exam_type_id')->nullable()->constrained()->nullOnDelete()->after('academic_year_id');
            $table->foreignId('governorate_id')->nullable()->constrained()->nullOnDelete()->after('exam_type_id');
            
            // Staging & Processing info
            $table->json('mapping_data')->nullable()->after('error_message')->comment('Stores the column mapping configuration');
            $table->integer('processed_rows')->default(0)->after('records_count');
            $table->integer('successful_rows')->default(0)->after('processed_rows');
            $table->integer('failed_rows')->default(0)->after('successful_rows');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['exam_type_id']);
            $table->dropForeign(['governorate_id']);
            $table->dropColumn([
                'batch_name', 
                'file_path', 
                'academic_year_id', 
                'exam_type_id', 
                'governorate_id', 
                'mapping_data',
                'processed_rows',
                'successful_rows',
                'failed_rows'
            ]);
        });
    }
};
