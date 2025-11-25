<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageSiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-site-settings';
    
    protected static ?string $navigationLabel = 'Site Settings';
    
    protected static ?string $title = 'Site Settings';
    
    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General & SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('site_title')
                                    ->label('Site Title')
                                    ->placeholder('e.g., Laurensius Dimas Portfolio')
                                    ->helperText('Default meta title for the site'),
                                
                                Forms\Components\Textarea::make('site_description')
                                    ->label('Site Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Default meta description (160 chars max)'),
                                
                                Forms\Components\FileUpload::make('favicon_url')
                                    ->label('Favicon')
                                    ->directory('settings')
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Upload a square image (32x32 or 64x64)'),
                                
                                Forms\Components\FileUpload::make('og_image_url')
                                    ->label('Default Social Share Image')
                                    ->directory('settings/og-images')
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Default OG image for social sharing (1200x630 recommended)'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Hero Section')
                            ->icon('heroicon-o-film')
                            ->schema([
                                Forms\Components\FileUpload::make('hero_video_url')
                                    ->label('Hero Video/GIF')
                                    ->directory('settings/hero')
                                    ->helperText('Upload video or GIF for the landing intro'),
                                
                                Forms\Components\TextInput::make('hero_headline')
                                    ->label('Headline')
                                    ->placeholder('e.g., Creative Director & Motion Designer')
                                    ->columnSpanFull(),
                                
                                Forms\Components\TextInput::make('hero_subheadline')
                                    ->label('Sub-headline')
                                    ->placeholder('e.g., Crafting visual stories that move')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Profile')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Forms\Components\FileUpload::make('profile_picture_url')
                                    ->label('Profile Picture')
                                    ->directory('settings/profile')
                                    ->image()
                                    ->imageEditor()
                                    ->avatar()
                                    ->helperText('Your profile photo'),
                                
                                Forms\Components\Textarea::make('bio_short')
                                    ->label('Short Bio')
                                    ->rows(2)
                                    ->maxLength(200)
                                    ->helperText('Brief introduction (200 chars max)'),
                                
                                Forms\Components\RichEditor::make('bio_long')
                                    ->label('Full Bio')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->columnSpanFull()
                                    ->helperText('Detailed biography with formatting'),
                                
                                Forms\Components\FileUpload::make('resume_url')
                                    ->label('Resume/CV')
                                    ->directory('settings/resume')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->helperText('Upload your resume as PDF'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Sections')
                            ->icon('heroicon-o-rectangle-group')
                            ->schema([
                                Forms\Components\Fieldset::make('Portfolio')
                                    ->schema([
                                        Forms\Components\TextInput::make('portfolio_heading')
                                            ->label('Portfolio Heading')
                                            ->placeholder('CURATED PROJECTS'),

                                        Forms\Components\TextInput::make('portfolio_subheading')
                                            ->label('Portfolio Subheading')
                                            ->placeholder('Optional supporting copy'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Fieldset::make('Corporate Highlight')
                                    ->schema([
                                        Forms\Components\TextInput::make('corporate_heading')
                                            ->label('Corporate Heading')
                                            ->placeholder('CORPORATE PROJECTS'),

                                        Forms\Components\TextInput::make('corporate_subheading')
                                            ->label('Corporate Subheading')
                                            ->placeholder('ESCO LIFESCIENCES GROUP'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Fieldset::make('Archive')
                                    ->schema([
                                        Forms\Components\TextInput::make('older_heading')
                                            ->label('Older Projects Heading')
                                            ->placeholder('OLDER PROJECTS'),

                                        Forms\Components\TextInput::make('older_subheading')
                                            ->label('Older Projects Subheading')
                                            ->placeholder('2019-2023'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label('Contact Email')
                                    ->placeholder('hello@example.com')
                                    ->helperText('Primary contact email'),
                                
                                Forms\Components\Repeater::make('social_links')
                                    ->label('Social Media Links')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform')
                                            ->label('Platform')
                                            ->placeholder('e.g., Instagram, LinkedIn, Behance')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Icon (optional)')
                                            ->placeholder('heroicon-o-link')
                                            ->helperText('Heroicon name or leave empty'),
                                    ])
                                    ->columns(3)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                                    ->columnSpanFull()
                                    ->helperText('Add your social media profiles'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = SiteSetting::current();
        $settings->update($data);

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('Site settings have been updated successfully.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
