<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_types', 'iframe_crop_right')) {
                $table->integer('iframe_crop_right')->default(0)->after('iframe_crop_left');
            }
            if (!Schema::hasColumn('exam_types', 'iframe_crop_bottom')) {
                $table->integer('iframe_crop_bottom')->default(0)->after('iframe_crop_right');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['iframe_crop_right', 'iframe_crop_bottom']);
        });
    }
};
