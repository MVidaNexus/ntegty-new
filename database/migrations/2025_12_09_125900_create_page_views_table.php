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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('page_type')->nullable(); // home, search, result, governorate
            $table->foreignId('governorate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('device_type', 20)->nullable(); // mobile, tablet, desktop
            $table->string('browser', 50)->nullable();
            $table->timestamps();
            
            $table->index(['created_at', 'page_type']);
            $table->index('path');
            $table->index('governorate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
