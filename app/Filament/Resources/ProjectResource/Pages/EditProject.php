<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\WithFileUploads;

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
            return $path;
        }
        return null;
    }
}
