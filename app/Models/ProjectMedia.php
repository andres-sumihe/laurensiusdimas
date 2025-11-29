<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMedia extends Model
{
    use HasFactory;
    use HasFileUploads;

    protected $table = 'project_media';

    protected $fillable = [
        'project_id',
        'type',
        'layout',
        'slot_index',
        'url',
        'thumbnail_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'slot_index' => 'integer',
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * File upload fields for automatic cleanup.
     */
    public function getFileUploadFields(): array
    {
        return ['url', 'thumbnail_url'];
    }

    /**
     * Scope for corporate media (has layout)
     */
    public function scopeCorporate($query)
    {
        return $query->whereNotNull('layout');
    }

    /**
     * Scope for regular media (grid-based, has slot_index)
     */
    public function scopeRegular($query)
    {
        return $query->whereNotNull('slot_index');
    }

    /**
     * Scope for landscape layout
     */
    public function scopeLandscape($query)
    {
        return $query->where('layout', 'landscape');
    }

    /**
     * Scope for portrait layout
     */
    public function scopePortrait($query)
    {
        return $query->where('layout', 'portrait');
    }
}
