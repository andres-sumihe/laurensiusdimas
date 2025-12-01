<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\ProjectMedia;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Models\Project;

class EditProject extends EditRecord
{
    use WithFileUploads;

    protected static string $resource = ProjectResource::class;

    /**
     * Prevent changing section to "older" if another older project exists
     */
    protected function beforeSave(): void
    {
        if (($this->data['section'] ?? '') === 'older') {
            // Check if another older project exists (excluding current record)
            $olderExists = Project::where('section', 'older')
                ->where('id', '!=', $this->record->id)
                ->exists();
            
            if ($olderExists) {
                Notification::make()
                    ->danger()
                    ->title('Cannot Change to Older Project')
                    ->body('Another Older Projects entry already exists. Only one Older Projects section is allowed.')
                    ->persistent()
                    ->send();
                
                $this->halt();
            }
        }
    }

    public $mediaUpload;
    public $thumbnailUpload;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function updatedMediaUpload()
    {
        if ($this->mediaUpload) {
            $path = $this->mediaUpload->store('project-media', 'public');
            
            // Dispatch event with the stored path
            $this->dispatch('media-uploaded', path: $path);
            
            // Clear the upload
            $this->mediaUpload = null;
            
            return $path;
        }
        return null;
    }

    public function updatedThumbnailUpload()
    {
        if ($this->thumbnailUpload) {
            $path = $this->thumbnailUpload->store('project-media/thumbnails', 'public');
            
            // Dispatch event with the stored path
            $this->dispatch('thumbnail-uploaded', path: $path);
            
            // Clear the upload
            $this->thumbnailUpload = null;
            
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
     * Save grid media to project_media table after record is saved
     */
    protected function afterSave(): void
    {
        $section = $this->data['section'] ?? 'curated';
        
        // Only process grid media for curated/older sections
        if (!in_array($section, ['curated', 'older'])) {
            return;
        }

        $mediaItems = $this->data['grid_media'] ?? [];
        
        // Get existing grid media IDs for this project
        $existingMedia = $this->record->gridMedia;
        $existingIds = $existingMedia->pluck('id')->toArray();
        $processedIds = [];

        foreach ($mediaItems as $index => $item) {
            if (empty($item['url'])) {
                continue;
            }

            $mediaData = [
                'project_id' => $this->record->id,
                'type' => $item['type'] ?? 'image',
                'url' => $item['url'],
                'thumbnail_url' => $item['thumbnailUrl'] ?? null,
                'slot_index' => $index,
                'sort_order' => $index,
                'layout' => null, // Grid media doesn't use layout field
            ];

            if (!empty($item['id'])) {
                // Update existing record
                $media = ProjectMedia::find($item['id']);
                if ($media) {
                    // Check if URL changed to delete old file
                    if ($media->url !== $item['url']) {
                        $this->deleteMediaFile($media->url);
                    }
                    $media->update($mediaData);
                    $processedIds[] = $media->id;
                }
            } else {
                // Create new record
                $media = ProjectMedia::create($mediaData);
                $processedIds[] = $media->id;
            }
        }

        // Delete removed media records and their files
        $removedIds = array_diff($existingIds, $processedIds);
        foreach ($removedIds as $id) {
            $media = ProjectMedia::find($id);
            if ($media) {
                $this->deleteMediaFile($media->url);
                if ($media->thumbnail_url) {
                    $this->deleteMediaFile($media->thumbnail_url);
                }
                $media->delete();
            }
        }
    }
}
