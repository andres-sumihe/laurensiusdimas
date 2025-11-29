<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditProject extends EditRecord
{
    use WithFileUploads;

    protected static string $resource = ProjectResource::class;

    public $mediaUpload;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

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
     * Clean up orphaned media files when the record is saved
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Get old media items
        $oldMediaItems = $this->record->media_items ?? [];
        $newMediaItems = $data['media_items'] ?? [];

        // Find removed media items and delete them
        $oldUrls = collect($oldMediaItems)->pluck('url')->filter()->toArray();
        $newUrls = collect($newMediaItems)->pluck('url')->filter()->toArray();
        
        $removedUrls = array_diff($oldUrls, $newUrls);
        
        foreach ($removedUrls as $url) {
            $this->deleteMediaFile($url);
        }

        return $data;
    }
}
