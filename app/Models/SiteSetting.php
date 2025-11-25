<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_title',
        'site_description',
        'favicon_url',
        'og_image_url',
        'hero_video_url',
        'hero_headline',
        'hero_subheadline',
        'portfolio_heading',
        'portfolio_subheading',
        'corporate_heading',
        'corporate_subheading',
        'older_heading',
        'older_subheading',
        'profile_picture_url',
        'bio_short',
        'bio_long',
        'resume_url',
        'email',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    /**
     * Get the singleton instance (create if doesn't exist)
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
