<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;
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
                                    ->label('Site Logo / Favicon')
                                    ->directory('settings')
                                    ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg', 'image/x-icon'])
                                    ->maxSize(2048) // 2MB
                                    ->helperText('Upload SVG (recommended) or PNG/ICO. SVG will be saved as /logo.svg for reuse across the site.'),
                                
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
                                    ->disk('public')
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(25600) // 25MB in KB
                                    ->acceptedFileTypes(['image/gif', 'video/mp4', 'video/webm', 'video/quicktime'])
                                    ->helperText('Upload video (MP4/WebM) or GIF for the landing intro. Max size: 25MB.')
                                    ->columnSpanFull(),
                                
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

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('older_year_from')
                                                    ->label('Year From')
                                                    ->numeric()
                                                    ->minValue(1990)
                                                    ->maxValue(2100)
                                                    ->placeholder('2019'),
                                                
                                                Forms\Components\TextInput::make('older_year_to')
                                                    ->label('Year To')
                                                    ->numeric()
                                                    ->minValue(1990)
                                                    ->maxValue(2100)
                                                    ->placeholder('2023'),
                                            ]),
                                    ])
                                    ->columns(1),
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
                        Forms\Components\Tabs\Tab::make('Footer')
                            ->icon('heroicon-o-rectangle-group')
                            ->schema([
                                Forms\Components\Textarea::make('footer_text')
                                    ->label('Footer Text')
                                    ->rows(3)
                                    ->placeholder('Short footer copy or tagline'),

                                Forms\Components\TextInput::make('footer_cta_label')
                                    ->label('Footer CTA Label')
                                    ->placeholder('Email me')
                                    ->maxLength(120),

                                Forms\Components\TextInput::make('footer_cta_url')
                                    ->label('Footer CTA URL')
                                    ->placeholder('mailto:hello@example.com or https://...')
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Server-side check for hero file size
        if (!empty($data['hero_video_url']) && !str_starts_with($data['hero_video_url'], 'http')) {
            $path = $data['hero_video_url'];
            if (Storage::disk('public')->exists($path)) {
                $size = Storage::disk('public')->size($path);
                if ($size > 25 * 1024 * 1024) {
                    Notification::make()
                        ->danger()
                        ->title('Upload failed: File too large')
                        ->body('Hero file is too large. Maximum allowed size is 25MB.')
                        ->send();
                    return;
                }
            }
        }

        // Copy SVG favicon to public/logo.svg for easy reuse
        if (!empty($data['favicon_url']) && !str_starts_with($data['favicon_url'], 'http')) {
            $faviconPath = $data['favicon_url'];
            if (Storage::disk('public')->exists($faviconPath)) {
                $extension = pathinfo($faviconPath, PATHINFO_EXTENSION);
                
                // If it's an SVG, copy to public/logo.svg
                if (strtolower($extension) === 'svg') {
                    $faviconFullPath = Storage::disk('public')->path($faviconPath);
                    $publicLogoPath = public_path('logo.svg');
                    
                    // Copy the file
                    if (file_exists($faviconFullPath)) {
                        copy($faviconFullPath, $publicLogoPath);
                        
                        Notification::make()
                            ->success()
                            ->title('Logo updated')
                            ->body('SVG logo has been saved to /logo.svg for use across the site.')
                            ->send();
                    }
                }
            }
        }

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
