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
            // SEO Fields
            $table->string('seo_title')->nullable()->after('is_declared');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('seo_keywords')->nullable()->after('seo_description');
            
            // Content Box Fields
            $table->boolean('show_content_section')->default(true)->after('seo_keywords');
            $table->string('content_title')->nullable()->after('show_content_section');
            $table->text('content_intro')->nullable()->after('content_title');
            $table->longText('content_body')->nullable()->after('content_intro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description', 
                'seo_keywords',
                'show_content_section',
                'content_title',
                'content_intro',
                'content_body',
            ]);
        });
    }
};
