<?php

namespace App\Filament\Forms\Components;

use App\Models\CorporateProjectMedia;
use Filament\Forms\Components\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component as LivewireComponent;

class CorporateMediaTable extends LivewireComponent implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?int $projectId = null;

    public function mount(?int $projectId = null): void
    {
        $this->projectId = $projectId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CorporateProjectMedia::query()
                    ->when($this->projectId, fn (Builder $query) => $query->where('project_id', $this->projectId))
            )
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('layout')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->square()
                    ->size(60),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ]),
                
                Tables\Filters\SelectFilter::make('layout')
                    ->options([
                        'landscape' => 'Landscape',
                        'portrait' => 'Portrait',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New project media')
                    ->modalHeading('Add Project Media')
                    ->modalWidth('lg')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options([
                                'image' => 'Image',
                                'video' => 'Video',
                            ])
                            ->default('image')
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\Select::make('layout')
                            ->options([
                                'landscape' => 'Landscape',
                                'portrait' => 'Portrait',
                            ])
                            ->default('landscape')
                            ->required(),
                        
                        Forms\Components\FileUpload::make('url')
                            ->label('Media File')
                            ->directory('corporate-media')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/*', 'video/*'])
                            ->required()
                            ->columnSpanFull()
                            ->image()
                            ->imageEditor(),
                        
                        Forms\Components\FileUpload::make('thumbnail_url')
                            ->label('Video Thumbnail (optional)')
                            ->directory('corporate-media/thumbnails')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->visible(fn (callable $get) => $get('type') === 'video')
                            ->columnSpanFull(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->projectId;
                        $data['sort_order'] = CorporateProjectMedia::where('project_id', $this->projectId)->max('sort_order') + 1;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Media')
                    ->modalWidth('lg')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options([
                                'image' => 'Image',
                                'video' => 'Video',
                            ])
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\Select::make('layout')
                            ->options([
                                'landscape' => 'Landscape',
                                'portrait' => 'Portrait',
                            ])
                            ->required(),
                        
                        Forms\Components\FileUpload::make('url')
                            ->label('Media File')
                            ->directory('corporate-media')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/*', 'video/*'])
                            ->required()
                            ->columnSpanFull()
                            ->image()
                            ->imageEditor(),
                        
                        Forms\Components\FileUpload::make('thumbnail_url')
                            ->label('Video Thumbnail (optional)')
                            ->directory('corporate-media/thumbnails')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->visible(fn (callable $get) => $get('type') === 'video')
                            ->columnSpanFull(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->emptyStateHeading('No project media')
            ->emptyStateDescription('Create a project media to get started.');
    }

    public function render()
    {
        return view('filament.forms.components.corporate-media-table');
    }
}
