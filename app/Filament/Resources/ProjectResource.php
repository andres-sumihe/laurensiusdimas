<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Forms\Components\MediaLayoutPicker;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from title, but editable'),
                        
                        Forms\Components\Select::make('work_type')
                            ->label('Work Type')
                            ->options([
                                'freelance' => 'Freelance',
                                'full-time' => 'Full-time',
                                'contract' => 'Contract',
                                'personal' => 'Personal Project',
                                'collaboration' => 'Collaboration',
                            ])
                            ->placeholder('Select work type')
                            ->native(false),
                        
                        Forms\Components\TextInput::make('work_description')
                            ->label('Work Description')
                            ->maxLength(255)
                            ->placeholder('e.g., Motion Design, 3D Animation'),

                        Forms\Components\Select::make('section')
                            ->label('Section')
                            ->options(function (callable $get, ?Project $record) {
                                $options = [
                                    'curated' => 'Curated Projects',
                                    'corporate' => 'Corporate Projects',
                                ];
                                
                                // Check if an older project already exists
                                $olderExists = Project::where('section', 'older')
                                    ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->exists();
                                
                                // Only show older option if none exists, or if current record is the older project
                                if (!$olderExists || ($record && $record->section === 'older')) {
                                    $options['older'] = 'Older Projects';
                                }
                                
                                return $options;
                            })
                            ->default('curated')
                            ->required()
                            ->reactive() // Makes other fields respond to changes
                            ->helperText(function (callable $get, ?Project $record) {
                                $olderExists = Project::where('section', 'older')
                                    ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->exists();
                                
                                if ($olderExists && (!$record || $record->section !== 'older')) {
                                    return 'Corporate projects require a client connection. Note: Older Projects section is limited to one project.';
                                }
                                return 'Corporate projects require a client connection.';
                            }),

                        // Client field - ONLY visible for corporate projects
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (callable $get) => $get('section') === 'corporate')
                            ->visible(fn (callable $get) => $get('section') === 'corporate')
                            ->helperText('Select the corporate client for this project.')
                            ->validationMessages([
                                'required' => 'A client must be selected for corporate projects.',
                            ]),

                        Forms\Components\Select::make('layout')
                            ->label('Layout')
                            ->options(fn (callable $get) => $get('section') === 'older' 
                                ? ['single' => 'Single (Hero)']
                                : [
                                    'single' => 'Single (Hero)',
                                    'two' => 'Two (Split)',
                                    'three_two' => 'Three-Two (5-Up)',
                                    'three_three' => 'Three-Three (6-Up)',
                                    'four_one' => 'Four-One (5-Up)',
                                    'four_two' => 'Four-Two (6-Up)',
                                ])
                            ->default(fn (callable $get) => $get('section') === 'older' ? 'single' : 'three_two')
                            ->required(fn (callable $get) => in_array($get('section'), ['curated', 'older']))
                            ->nullable() // Allow null values
                            ->hidden(fn (callable $get) => $get('section') === 'corporate')
                            ->live(debounce: 100)
                            ->helperText(fn (callable $get) => $get('section') === 'older'
                                ? 'Older projects use Single (Hero) layout only.'
                                : 'Choose layout first, then add media to each slot below.'),
                        
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                            ]),
                    ])
                    ->columns(2),

                // Visual Media Picker for Curated and Older projects (uses project_media table via gridMedia relationship)
                Forms\Components\Section::make('Project Media')
                    ->description('Add images/videos for this project. Use slot numbers to control grid placement based on the selected layout.')
                    ->schema([
                        MediaLayoutPicker::make('grid_media')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn (callable $get) => in_array($get('section'), ['curated', 'older'])),

                // Corporate Media Table for Corporate projects (uses project_media table)
                Forms\Components\Section::make('Corporate Media')
                    ->description('Add images/videos for this corporate project. Each media can be landscape or portrait layout.')
                    ->schema([
                        Forms\Components\View::make('filament.forms.components.corporate-media-table')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn (callable $get) => $get('section') === 'corporate'),

                Forms\Components\Section::make('SEO Settings')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('Leave empty to use project title'),
                        
                        Forms\Components\Textarea::make('meta_description')
                            ->maxLength(160)
                            ->rows(3),
                        
                        Forms\Components\FileUpload::make('og_image')
                            ->label('Social Share Image')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('projects/og-images')
                            ->image()
                            ->imageEditor(),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Visibility & Ordering')
                    ->schema([
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Published')
                            ->default(true)
                            ->live(debounce: 0) // Force live update
                            ->helperText('Toggle to show/hide on public site'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('section')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'curated' => 'success',
                        'corporate' => 'info',
                        'older' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('layout')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->label('Last Updated'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Published')
                    ->boolean()
                    ->trueLabel('Published only')
                    ->falseLabel('Drafts only')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            // Corporate media is now embedded inline via Livewire component
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
