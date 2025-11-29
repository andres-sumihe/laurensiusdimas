<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Validation\ValidationException;

class MediaLayoutPicker extends Field
{
    protected string $view = 'filament.forms.components.media-layout-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        // Add validation using Filament's afterStateUpdated or dehydration
        $this->dehydrateStateUsing(function ($state) {
            return $state;
        });

        // Custom validation via rule
        $this->rule(function () {
            return function (string $attribute, $value, $fail) {
                $layout = $this->getLivewire()->data['layout'] ?? 'three_two';
                $requiredCount = $this->getSlotCountForLayout($layout);
                
                $filledCount = 0;
                if (is_array($value)) {
                    foreach ($value as $item) {
                        if (is_array($item) && !empty($item['url'])) {
                            $filledCount++;
                        }
                    }
                }
                
                if ($filledCount < $requiredCount) {
                    $fail("Media gallery requires {$requiredCount} items for the selected layout. Currently {$filledCount} filled.");
                }
            };
        });
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
            'landscape' => [
                ['cols' => 12, 'label' => '1'],
            ],
            'portrait' => [
                ['cols' => 12, 'label' => '1'],
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

    public function getSlotCountForLayout(string $layout): int
    {
        return match ($layout) {
            'single', 'landscape', 'portrait' => 1,
            'two' => 2,
            'three_two', 'four_one' => 5,
            'three_three', 'four_two' => 6,
            default => 1,
        };
    }
}
