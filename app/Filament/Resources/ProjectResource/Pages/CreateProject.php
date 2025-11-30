<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\ProjectMedia;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CreateProject extends CreateRecord
{
    use WithFileUploads;

    protected static string $resource = ProjectResource::class;

    public $mediaUpload;

    public function updatedMediaUpload()
    {
        if ($this->mediaUpload) {
            $path = $this->mediaUpload->store('projects/media', 'public');
            
            // Dispatch event with the stored path
            $this->dispatch('media-uploaded', path: $path);
            
            // Clear the upload
            $this->mediaUpload = null;
            
            return $path;
        }
        return null;
    }

    /**
     * Delete a media file from storage
     */
    public function deleteMediaFile(string $path): void
    {
        if ($path && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Save grid media to project_media table after record is created
     */
    protected function afterCreate(): void
    {
        $section = $this->data['section'] ?? 'curated';
        
        // Only process grid media for curated/older sections
        if (!in_array($section, ['curated', 'older'])) {
            return;
        }

        $mediaItems = $this->data['grid_media'] ?? [];

        foreach ($mediaItems as $index => $item) {
            if (empty($item['url'])) {
                continue;
            }

            ProjectMedia::create([
                'project_id' => $this->record->id,
                'type' => $item['type'] ?? 'image',
                'url' => $item['url'],
                'thumbnail_url' => $item['thumbnailUrl'] ?? null,
                'slot_index' => $index,
                'sort_order' => $index,
                'layout' => null, // Grid media doesn't use layout field
            ]);
        }
    }
}
