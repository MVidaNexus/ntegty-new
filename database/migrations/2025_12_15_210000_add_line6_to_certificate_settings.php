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
            // السطر السادس للذكور
            $table->text('line6_text_male')->nullable()->after('line5_position_y');
            $table->string('line6_font_family')->default('Cairo')->after('line6_text_male');
            $table->integer('line6_font_size')->default(50)->after('line6_font_family');
            $table->string('line6_color')->default('#374151')->after('line6_font_size');
            $table->integer('line6_position_x')->default(1240)->after('line6_color');
            $table->integer('line6_position_y')->default(1400)->after('line6_position_x');
            // السطر السادس للإناث
            $table->text('line6_text_female')->nullable()->after('line6_position_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $table->dropColumn([
                'line6_text_male',
                'line6_font_family',
                'line6_font_size',
                'line6_color',
                'line6_position_x',
                'line6_position_y',
                'line6_text_female',
            ]);
        });
    }
};
