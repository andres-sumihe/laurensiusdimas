<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'description',
        'layout',
        'section',
        'media_items',
        'is_visible',
        'sort_order',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'media_items' => 'array',
        'is_visible' => 'boolean',
    ];
    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            // Auto-detect layout for Corporate projects if not manually set (or even if set, to ensure correctness if desired)
            // Here we'll only do it if it's 'corporate'
            if ($project->section === 'corporate') {
                $media = $project->media_items[0] ?? null;
                
                if ($media && isset($media['url'])) {
                    // If we have explicit width/height in metadata (future proofing)
                    if (isset($media['width']) && isset($media['height'])) {
                        $ratio = $media['width'] / $media['height'];
                        $project->layout = $ratio >= 1 ? 'landscape' : 'portrait';
                    } 
                    // Otherwise try to detect from file if local
                    elseif (!str_starts_with($media['url'], 'http')) {
                        try {
                            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($media['url']);
                            if (file_exists($path)) {
                                [$width, $height] = getimagesize($path);
                                $ratio = $width / $height;
                                $project->layout = $ratio >= 1 ? 'landscape' : 'portrait';
                            }
                        } catch (\Exception $e) {
                            // Fallback or log error
                        }
                    }
                    // If remote URL (dummy data), we can't easily detect without fetching.
                    // But for dummy data we set it explicitly in the seeder/script.
                }
            }
        });
    }
}
