# Copilot Instructions — Laurensius Dimas Portfolio

## Architecture Overview
This is a **Laravel 12 + FilamentPHP v3** portfolio/CMS monolith using the TALL stack (Tailwind, Alpine.js, Livewire, Laravel). The public site is a single-page Livewire component; the admin panel is entirely Filament-based at `/admin`.

### Key Data Flow
- **Public Frontend**: `App\Livewire\Home` renders all content via `SiteSetting::current()`, `Project`, and `Client` models
- **Admin Panel**: Filament Resources (`ProjectResource`, `ClientResource`) + custom Page (`ManageSiteSettings`)
- **Media Storage**: Local filesystem via `storage/app/public`, accessed with `Storage::url()`

## Models & Patterns

### Singleton Pattern for Site Settings
`SiteSetting::current()` returns the singleton config row (ID=1). Always use this helper:
```php
$settings = SiteSetting::current();
$settings->update(['hero_headline' => 'New Title']);
```

### Media Items (JSON Column)
Projects store media as JSON array in `media_items`. Structure:
```php
[
    ['type' => 'image|video', 'url' => 'path/to/file.jpg', 'thumbnailUrl' => 'path/to/thumb.jpg']
]
```
Handle URL resolution in views: check `str_starts_with($url, 'http')` before `Storage::url()`.

### Project Layouts
Layout types control grid rendering in `home.blade.php`:
- `single` — 1 item (col-span-12)
- `two` — 2 items (col-span-6 each)  
- `three_two` — Row 1: 3 items (col-span-4), Row 2: 2 items (col-span-6)
- `three_three`, `four_one`, `four_two` — see blade template

## Developer Commands

```bash
# Start all services (server, queue, logs, vite) concurrently
composer dev

# Frontend build
npm run dev   # development
npm run build # production
```

## Filament Admin Conventions

### Resources Location
- `app/Filament/Resources/` — CRUD resources (ProjectResource, ClientResource)
- `app/Filament/Pages/` — Custom pages (ManageSiteSettings)

### Form Patterns Used
- `Forms\Components\Repeater` for JSON arrays (media_items, social_links)
- `Forms\Components\FileUpload` with `->directory()` for organized storage
- `Forms\Components\Select::make('layout')` with predefined options
- `->live(onBlur: true)` + `afterStateUpdated` for auto-slug generation

### Table Features
- `->reorderable('sort_order')` enables drag-and-drop in tables
- Use `TernaryFilter` for published/draft filtering

## View Structure

- `resources/views/livewire/home.blade.php` — Main public page (all sections)
- `resources/views/components/layouts/app.blade.php` — App layout wrapper
- `resources/views/filament/pages/` — Custom Filament page views

## Database Conventions

- Migrations use `is_visible` (boolean) + `sort_order` (integer) for content ordering
- Store structured data as JSON columns (`media_items`, `social_links`)
- Use snake_case for column names; Eloquent handles casting via `$casts`

## Deployment Notes

Target: **Hostinger Shared Hosting** (PHP 8.3, MySQL 5.7)
- Media stored locally in `storage/app/public` (symlinked to `public/storage`)
- No queue workers in production; use sync driver

## Video Performance Pattern

When adding video elements, follow the lazy-loading pattern in `home.blade.php`:
```blade
<video autoplay muted loop playsinline poster="{{ $thumbnail }}">
    <source src="{{ $url }}" type="video/mp4">
</video>
```
- Always include `poster` attribute for video thumbnails
- Use `playsinline` for mobile compatibility
- Consider Intersection Observer for viewport-based loading on pages with many videos
