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
        Schema::table('certificate_settings', function (Blueprint $table) {
            // موضع الاسم للإناث
            if (!Schema::hasColumn('certificate_settings', 'name_position_x_female')) {
                $table->integer('name_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'name_position_y_female')) {
                $table->integer('name_position_y_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'name_font_family')) {
                $table->string('name_font_family')->default('Cairo');
            }
            
            // السطر 1 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line1_font_family_female')) {
                $table->string('line1_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line1_font_size_female')) {
                $table->integer('line1_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line1_color_female')) {
                $table->string('line1_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line1_position_x_female')) {
                $table->integer('line1_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line1_position_y_female')) {
                $table->integer('line1_position_y_female')->nullable();
            }
            
            // السطر 2 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line2_font_family_female')) {
                $table->string('line2_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line2_font_size_female')) {
                $table->integer('line2_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line2_color_female')) {
                $table->string('line2_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line2_position_x_female')) {
                $table->integer('line2_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line2_position_y_female')) {
                $table->integer('line2_position_y_female')->nullable();
            }
            
            // السطر 3 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line3_font_family_female')) {
                $table->string('line3_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line3_font_size_female')) {
                $table->integer('line3_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line3_color_female')) {
                $table->string('line3_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line3_position_x_female')) {
                $table->integer('line3_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line3_position_y_female')) {
                $table->integer('line3_position_y_female')->nullable();
            }
            
            // السطر 4 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line4_font_family_female')) {
                $table->string('line4_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line4_font_size_female')) {
                $table->integer('line4_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line4_color_female')) {
                $table->string('line4_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line4_position_x_female')) {
                $table->integer('line4_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line4_position_y_female')) {
                $table->integer('line4_position_y_female')->nullable();
            }
            
            // السطر 5 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line5_font_family_female')) {
                $table->string('line5_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line5_font_size_female')) {
                $table->integer('line5_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line5_color_female')) {
                $table->string('line5_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line5_position_x_female')) {
                $table->integer('line5_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line5_position_y_female')) {
                $table->integer('line5_position_y_female')->nullable();
            }
            
            // السطر 6 للإناث
            if (!Schema::hasColumn('certificate_settings', 'line6_font_family_female')) {
                $table->string('line6_font_family_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line6_font_size_female')) {
                $table->integer('line6_font_size_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line6_color_female')) {
                $table->string('line6_color_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line6_position_x_female')) {
                $table->integer('line6_position_x_female')->nullable();
            }
            if (!Schema::hasColumn('certificate_settings', 'line6_position_y_female')) {
                $table->integer('line6_position_y_female')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $columns = [
                'name_position_x_female',
                'name_position_y_female',
                'name_font_family',
                'line1_font_family_female',
                'line1_font_size_female',
                'line1_color_female',
                'line1_position_x_female',
                'line1_position_y_female',
                'line2_font_family_female',
                'line2_font_size_female',
                'line2_color_female',
                'line2_position_x_female',
                'line2_position_y_female',
                'line3_font_family_female',
                'line3_font_size_female',
                'line3_color_female',
                'line3_position_x_female',
                'line3_position_y_female',
                'line4_font_family_female',
                'line4_font_size_female',
                'line4_color_female',
                'line4_position_x_female',
                'line4_position_y_female',
                'line5_font_family_female',
                'line5_font_size_female',
                'line5_color_female',
                'line5_position_x_female',
                'line5_position_y_female',
                'line6_font_family_female',
                'line6_font_size_female',
                'line6_color_female',
                'line6_position_x_female',
                'line6_position_y_female',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('certificate_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
