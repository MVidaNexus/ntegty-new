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
        Schema::table('governorates', function (Blueprint $table) {
            $table->integer('iframe_crop_right')->default(0)->after('iframe_crop_left');
            $table->integer('iframe_crop_bottom')->default(0)->after('iframe_crop_right');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn(['iframe_crop_right', 'iframe_crop_bottom']);
        });
    }
};
