<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\ProjectMedia;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Models\Project;

class CreateProject extends CreateRecord
{
    use WithFileUploads;

    protected static string $resource = ProjectResource::class;

    /**
     * Prevent creating more than one "older" project
     */
    protected function beforeCreate(): void
    {
        if (($this->data['section'] ?? '') === 'older') {
            $olderExists = Project::where('section', 'older')->exists();
            
            if ($olderExists) {
                Notification::make()
                    ->danger()
                    ->title('Cannot Create Older Project')
                    ->body('An Older Projects entry already exists. Only one Older Projects section is allowed.')
                    ->persistent()
                    ->send();
                
                $this->halt();
            }
        }
    }

    /**
     * Mutate form data before creating - ensure corporate projects have null layout
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // For corporate projects, ensure layout is null
        if (($data['section'] ?? '') === 'corporate') {
            $data['layout'] = null;
        }
        
        return $data;
    }

    public $mediaUpload;
    public $thumbnailUpload;

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
