<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorporateMediaRelationManager extends RelationManager
{
    protected static string $relationship = 'corporateMedia';

    protected static ?string $title = 'Project Media';

    protected static ?string $modelLabel = 'project media';

    protected static ?string $pluralModelLabel = 'project media';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->directory('project-media')
                    ->disk('public')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/*', 'video/*'])
                    ->required()
                    ->columnSpanFull()
                    ->image()
                    ->imageEditor(),
                
                Forms\Components\FileUpload::make('thumbnail_url')
                    ->label('Thumbnail (optional)')
                    ->helperText('Custom thumbnail for video preview')
                    ->directory('project-media/thumbnails')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->visible(fn (callable $get) => $get('type') === 'video')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
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
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->modalWidth('lg'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Media')
                    ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
