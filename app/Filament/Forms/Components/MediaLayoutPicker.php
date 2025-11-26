<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class MediaLayoutPicker extends Field
{
    protected string $view = 'filament.forms.components.media-layout-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);
    }

    public function getLayoutSlots(): array
    {
        $layout = $this->getLivewire()->data['layout'] ?? 'three_two';

        return match ($layout) {
            'single' => [
                ['cols' => 12, 'label' => '1'],
            ],
            'two' => [
                ['cols' => 6, 'label' => '1'],
                ['cols' => 6, 'label' => '2'],
            ],
            'three_two' => [
                ['cols' => 4, 'label' => '1'],
                ['cols' => 4, 'label' => '2'],
                ['cols' => 4, 'label' => '3'],
                ['cols' => 6, 'label' => '4'],
                ['cols' => 6, 'label' => '5'],
            ],
            'three_three' => [
                ['cols' => 4, 'label' => '1'],
                ['cols' => 4, 'label' => '2'],
                ['cols' => 4, 'label' => '3'],
                ['cols' => 4, 'label' => '4'],
                ['cols' => 4, 'label' => '5'],
                ['cols' => 4, 'label' => '6'],
            ],
            'four_one' => [
                ['cols' => 3, 'label' => '1'],
                ['cols' => 3, 'label' => '2'],
                ['cols' => 3, 'label' => '3'],
                ['cols' => 3, 'label' => '4'],
                ['cols' => 12, 'label' => '5'],
            ],
            'four_two' => [
                ['cols' => 3, 'label' => '1'],
                ['cols' => 3, 'label' => '2'],
                ['cols' => 3, 'label' => '3'],
                ['cols' => 3, 'label' => '4'],
                ['cols' => 6, 'label' => '5'],
                ['cols' => 6, 'label' => '6'],
            ],
            default => [
                ['cols' => 12, 'label' => '1'],
            ],
        };
    }

    public function getSlotCount(): int
    {
        return count($this->getLayoutSlots());
    }
}
