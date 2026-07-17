<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Country;
use App\Models\ExamType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        if ($egypt) {
            ExamType::updateOrCreate(
                ['code' => 'azhar-primary'],
                [
                    'country_id' => $egypt->id,
                    'name_ar' => 'الابتدائية الأزهرية',
                    'name_en' => 'Azhar Primary Certificate',
                    'slug' => 'azhar-primary',
                    'level' => 'preparatory', // Restricted to preparatory/secondary by enum CHECK constraint
                    'result_service_type' => 'search',
                    'auto_calculate_status' => true,
                    'show_content_section' => true,
                    'show_popular_searches' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ExamType::where('code', 'azhar-primary')->delete();
    }
};
