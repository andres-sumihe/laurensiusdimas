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
}
