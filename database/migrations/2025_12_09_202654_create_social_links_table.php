<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            
            // Platform type: telegram, whatsapp, facebook, instagram, twitter, youtube, tiktok, etc.
            $table->string('platform');
            
            // The actual URL/link
            $table->string('url');
            
            // Custom label (optional)
            $table->string('label')->nullable();
            
            // Scope type: default, country, exam_type
            $table->enum('scope_type', ['default', 'country', 'exam_type'])->default('default');
            
            // Scope ID (country_id or exam_type_id) - null for default
            $table->unsignedBigInteger('scope_id')->nullable();
            
            // Display order
            $table->integer('sort_order')->default(0);
            
            // Is active?
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['scope_type', 'scope_id']);
            $table->index(['platform', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
