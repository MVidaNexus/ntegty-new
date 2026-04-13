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
        Schema::table('countries', function (Blueprint $table) {
            // SEO Fields
            $table->string('seo_title', 255)->nullable()->after('telegram_url');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords', 500)->nullable()->after('seo_description');
            
            // Content Fields
            $table->string('content_title', 255)->nullable()->after('seo_keywords');
            $table->text('content_intro')->nullable()->after('content_title');
            $table->text('content_body')->nullable()->after('content_intro');
            $table->boolean('show_content_section')->default(true)->after('content_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description', 
                'seo_keywords',
                'content_title',
                'content_intro',
                'content_body',
                'show_content_section',
            ]);
        });
    }
};
