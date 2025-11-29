<?php

namespace App\Livewire;

use App\Models\ProjectMedia;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ProjectMediaTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?int $projectId = null;
    public string $section = 'corporate'; // 'corporate', 'curated', 'older'

    public function mount(?int $projectId = null, string $section = 'corporate'): void
    {
        $this->projectId = $projectId;
        $this->section = $section;
    }

    protected function getTableQuery(): Builder
    {
        $query = ProjectMedia::query()
            ->when($this->projectId, fn (Builder $q) => $q->where('project_id', $this->projectId));

        // Filter based on section type
        if ($this->section === 'corporate') {
            $query->whereNotNull('layout');
        } else {
            $query->whereNotNull('slot_index');
        }

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New project media')
                    ->modalHeading('Add Project Media')
                    ->modalWidth('lg')
                    ->form($this->getMediaForm())
                    ->mutateFormDataUsing(fn (array $data) => $this->mutateCreateData($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Media')
                    ->modalWidth('lg')
                    ->mutateRecordDataUsing(fn (array $data) => $this->prepareEditData($data))
                    ->form($this->getMediaForm())
                    ->mutateFormDataUsing(fn (array $data) => $this->mutateEditData($data)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable($this->section === 'corporate' ? 'sort_order' : 'slot_index')
            ->defaultSort($this->section === 'corporate' ? 'sort_order' : 'slot_index')
            ->emptyStateHeading('No project media')
            ->emptyStateDescription('Create a project media to get started.');
    }

    protected function getTableColumns(): array
    {
        $columns = [
            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'image' => 'success',
                    'video' => 'info',
                    default => 'gray',
                })
                ->sortable(),
            
            Tables\Columns\ImageColumn::make('url')
                ->label('Preview')
                ->disk('public')
                ->square()
                ->size(60),
        ];

        // Add layout column for corporate projects
        if ($this->section === 'corporate') {
            array_splice($columns, 0, 0, [
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
            ]);
            array_splice($columns, 3, 0, [
                Tables\Columns\TextColumn::make('layout')
                    ->badge()
                    ->sortable(),
            ]);
        } else {
            // For curated/older projects, show slot index
            array_splice($columns, 0, 0, [
                Tables\Columns\TextColumn::make('slot_index')
                    ->label('Slot')
                    ->sortable(),
            ]);
        }

        return $columns;
    }

    protected function getTableFilters(): array
    {
        $filters = [
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'image' => 'Image',
                    'video' => 'Video',
                ]),
        ];

        if ($this->section === 'corporate') {
            $filters[] = Tables\Filters\SelectFilter::make('layout')
                ->options([
                    'landscape' => 'Landscape',
                    'portrait' => 'Portrait',
                ]);
        }

        return $filters;
    }

    protected function getMediaForm(): array
    {
        $form = [
            Forms\Components\Select::make('type')
                ->options([
                    'image' => 'Image',
                    'video' => 'Video',
                ])
                ->default('image')
                ->required()
                ->reactive(),
        ];

        // Layout field for corporate projects
        if ($this->section === 'corporate') {
            $form[] = Forms\Components\Select::make('layout')
                ->options([
                    'landscape' => 'Landscape',
                    'portrait' => 'Portrait',
                ])
                ->default('landscape')
                ->required();
        } else {
            // Slot index for grid-based layouts
            $form[] = Forms\Components\TextInput::make('slot_index')
                ->label('Grid Slot')
                ->numeric()
                ->helperText('Position in the grid (1-based)')
                ->required();
        }

        // Media source options
        $form = array_merge($form, [
            Forms\Components\Select::make('source_type')
                ->label('Media Source')
                ->options([
                    'file' => 'Upload File',
                    'url' => 'External URL',
                ])
                ->default('file')
                ->required()
                ->reactive()
                ->columnSpanFull(),
            
            Forms\Components\FileUpload::make('url')
                ->label('Media File')
                ->directory('project-media')
                ->disk('public')
                ->visibility('public')
                ->acceptedFileTypes(['image/*', 'video/*'])
                ->required(fn (callable $get) => $get('source_type') === 'file')
                ->visible(fn (callable $get) => $get('source_type') === 'file')
                ->columnSpanFull()
                ->image()
                ->imageEditor(),
            
            Forms\Components\TextInput::make('external_url')
                ->label('Media URL')
                ->url()
                ->placeholder('https://example.com/image.jpg')
                ->required(fn (callable $get) => $get('source_type') === 'url')
                ->visible(fn (callable $get) => $get('source_type') === 'url')
                ->columnSpanFull(),
            
            Forms\Components\FileUpload::make('thumbnail_url')
                ->label('Video Thumbnail (optional)')
                ->directory('project-media/thumbnails')
                ->disk('public')
                ->visibility('public')
                ->image()
                ->visible(fn (callable $get) => $get('type') === 'video' && $get('source_type') === 'file')
                ->columnSpanFull(),
            
            Forms\Components\TextInput::make('external_thumbnail_url')
                ->label('Thumbnail URL (optional)')
                ->url()
                ->placeholder('https://example.com/thumbnail.jpg')
                ->visible(fn (callable $get) => $get('type') === 'video' && $get('source_type') === 'url')
                ->columnSpanFull(),
        ]);

        return $form;
    }

    protected function mutateCreateData(array $data): array
    {
        $data['project_id'] = $this->projectId;
        
        if ($this->section === 'corporate') {
            $data['sort_order'] = ProjectMedia::where('project_id', $this->projectId)
                ->whereNotNull('layout')
                ->max('sort_order') + 1;
        } else {
            // If slot_index not provided, auto-increment
            if (empty($data['slot_index'])) {
                $data['slot_index'] = ProjectMedia::where('project_id', $this->projectId)
                    ->whereNotNull('slot_index')
                    ->max('slot_index') + 1;
            }
        }
        
        return $this->handleExternalUrls($data);
    }

    protected function prepareEditData(array $data): array
    {
        // Detect if current URL is external or local file
        $url = $data['url'] ?? '';
        $isExternal = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
        $data['source_type'] = $isExternal ? 'url' : 'file';
        if ($isExternal) {
            $data['external_url'] = $url;
            $data['url'] = null;
        }
        
        // Same for thumbnail
        $thumbnail = $data['thumbnail_url'] ?? '';
        if (str_starts_with($thumbnail, 'http://') || str_starts_with($thumbnail, 'https://')) {
            $data['external_thumbnail_url'] = $thumbnail;
            $data['thumbnail_url'] = null;
        }
        
        return $data;
    }

    protected function mutateEditData(array $data): array
    {
        return $this->handleExternalUrls($data);
    }

    protected function handleExternalUrls(array $data): array
    {
        // If using external URL, use that instead of file upload
        if (($data['source_type'] ?? 'file') === 'url') {
            $data['url'] = $data['external_url'] ?? null;
            if (!empty($data['external_thumbnail_url'])) {
                $data['thumbnail_url'] = $data['external_thumbnail_url'];
            }
        }
        
        // Clean up temporary fields
        unset($data['source_type'], $data['external_url'], $data['external_thumbnail_url']);
        
        return $data;
    }

    public function render()
    {
        return view('livewire.project-media-table');
    }
}
