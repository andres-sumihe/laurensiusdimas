<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
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
}
