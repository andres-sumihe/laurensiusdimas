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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            
            // General & SEO
            $table->string('site_title')->nullable();
            $table->text('site_description')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('og_image_url')->nullable();
            
            // Hero Section
            $table->string('hero_video_url')->nullable();
            $table->string('hero_headline')->nullable();
            $table->string('hero_subheadline')->nullable();
            
            // Profile
            $table->string('profile_picture_url')->nullable();
            $table->text('bio_short')->nullable();
            $table->text('bio_long')->nullable();
            $table->string('resume_url')->nullable();
            
            // Contact
            $table->string('email')->nullable();
            $table->json('social_links')->nullable(); // [{platform, url, icon}]
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
