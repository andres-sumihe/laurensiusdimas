<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Livewire\WithFileUploads;

class CreateProject extends CreateRecord
{
    use WithFileUploads;

    protected static string $resource = ProjectResource::class;

    public $mediaUpload;

    public function updatedMediaUpload()
    {
        if ($this->mediaUpload) {
            $path = $this->mediaUpload->store('projects/media', 'public');
            return $path;
        }
        return null;
    }
}
