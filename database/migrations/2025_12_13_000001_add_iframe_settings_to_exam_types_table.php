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
        Schema::table('exam_types', function (Blueprint $table) {
            // إعدادات الـ iframe
            $table->string('iframe_width')->nullable()->after('embed_code')->default('100%');
            $table->string('iframe_height')->nullable()->after('iframe_width')->default('600px');
            $table->enum('iframe_position', ['center', 'left', 'right'])->nullable()->after('iframe_height')->default('center');
            $table->boolean('iframe_scrolling')->default(true)->after('iframe_position');
            $table->boolean('iframe_border')->default(false)->after('iframe_scrolling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['iframe_width', 'iframe_height', 'iframe_position', 'iframe_scrolling', 'iframe_border']);
        });
    }
};
