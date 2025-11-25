<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
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
                        
                        Forms\Components\TextInput::make('subtitle')
                            ->maxLength(255)
                            ->label('Category / Subtitle')
                            ->placeholder('e.g., Motion Design'),

                        Forms\Components\Select::make('layout')
                            ->label('Layout')
                            ->options([
                                'three_two' => '3 top, 2 bottom (5-up)',
                                'three_two_tall' => '3 top, 2 bottom (tall lower)',
                                'two' => '2-up split',
                                'single' => 'Single hero',
                            ])
                            ->default('three_two')
                            ->required()
                            ->helperText('Choose how media items are arranged in the project card.'),
                        
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

                Forms\Components\Section::make('Media Gallery')
                    ->schema([
                        Forms\Components\Repeater::make('media_items')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'image' => 'Image',
                                        'video' => 'Video',
                                    ])
                                    ->required()
                                    ->live(),
                                
                                Forms\Components\FileUpload::make('url')
                                    ->label('File')
                                    ->directory('projects/media')
                                    ->image()
                                    ->imageEditor()
                                    ->required(),
                                
                                Forms\Components\FileUpload::make('thumbnailUrl')
                                    ->label('Thumbnail (for videos)')
                                    ->directory('projects/thumbnails')
                                    ->image()
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'video'),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['type'] ?? null)
                            ->columnSpanFull(),
                    ]),

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
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('subtitle')
                    ->searchable()
                    ->label('Category')
                    ->badge()
                    ->color('gray'),
                
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
            //
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
