<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    use HasFileUploads;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'work_type',
        'work_description',
        'description',
        'layout',
        'section',
        'is_visible',
        'sort_order',
        'meta_title',
        'meta_description',
        'og_image',
        'client_id',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * File upload fields for automatic cleanup.
     */
    public function getFileUploadFields(): array
    {
        return ['og_image'];
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all media for this project (unified relationship)
     */
    public function projectMedia(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('sort_order');
    }

    /**
     * Alias for corporate projects - gets media with layout (landscape/portrait)
     */
    public function corporateMedia(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMedia::class)->whereNotNull('layout')->orderBy('sort_order');
    }

    /**
     * Gets media for grid-based layouts (curated/older) - sorted by slot_index
     */
    public function gridMedia(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMedia::class)->whereNotNull('slot_index')->orderBy('slot_index');
    }

}
