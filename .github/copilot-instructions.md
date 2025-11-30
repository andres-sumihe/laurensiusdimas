# Copilot Instructions — Laurensius Dimas Portfolio

## Architecture Overview
This is a **Laravel 12 + FilamentPHP v3** portfolio/CMS monolith using the TALL stack (Tailwind CSS v4, Alpine.js, Livewire 3, Laravel). The public site is a single-page Livewire component; the admin panel is entirely Filament-based at `/admin`.

### Key Data Flow
- **Public Frontend**: `App\Livewire\Home` renders all content via `SiteSetting::current()`, `Project`, `ProjectMedia`, and `Client` models
- **Admin Panel**: Filament Resources (`ProjectResource`, `ClientResource`) + custom Page (`ManageSiteSettings`)
- **Media Storage**: Local filesystem via `public` disk, accessed with `Storage::url()`

## Models & Patterns

### Singleton Pattern for Site Settings
`SiteSetting::current()` returns the singleton config row (ID=1). Always use this helper:
```php
$settings = SiteSetting::current();
$settings->update(['hero_headline' => 'New Title']);
```

### Unified Project Media (ProjectMedia Model)
Projects use a unified `project_media` table instead of JSON columns. Each media item has:
- `project_id` — Foreign key to project
- `type` — `image` or `video`
- `url` — File path (local) or full URL (external)
- `thumbnail_url` — Optional thumbnail for videos
- `layout` — `landscape` or `portrait` (for corporate projects)
- `sort_order` — Display order

Access via relationship: `$project->projectMedia` or `$project->corporateMedia`

### Automatic File Cleanup (HasFileUploads Trait)
Models with file uploads use `App\Traits\HasFileUploads` for automatic cleanup:
- Old files are deleted when replaced during update
- All files are deleted when record is deleted
- External URLs (http/https) are skipped

Models using this trait: `Client`, `SiteSetting`, `ProjectMedia`, `Project`

Implement in model:
```php
use App\Traits\HasFileUploads;

class MyModel extends Model
{
    use HasFileUploads;
    
    public function getFileUploadFields(): array
    {
        return ['logo_url', 'image_url']; // fields containing file paths
    }
}
```

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
npm run dev   # development with HMR
npm run build # production build (MUST run before deploy)
```

## Filament Admin Conventions

### Resources Location
- `app/Filament/Resources/` — CRUD resources (ProjectResource, ClientResource)
- `app/Filament/Pages/` — Custom pages (ManageSiteSettings)

### Form Patterns Used
- `Forms\Components\Repeater` for JSON arrays (social_links)
- `Forms\Components\FileUpload` with `->disk('public')->visibility('public')` — **REQUIRED for production**
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
- Store structured data as JSON columns (`social_links`) or related tables (`project_media`)
- Use snake_case for column names; Eloquent handles casting via `$casts`

---

## 🚨 Deployment — Hostinger Shared Hosting

### Environment Differences

| Feature | Local Development | Production (Hostinger) |
|---------|-------------------|------------------------|
| Storage symlink | `php artisan storage:link` works | ❌ `symlink()` disabled |
| Storage path | `storage/app/public/` | `public/storage/` (direct) |
| npm/node | Available | ❌ Not available |
| SSH | N/A | ❌ Not available |
| Queue driver | `database` | `sync` (no workers) |

### Storage Configuration

**Local (.env):**
```env
# No FILESYSTEM_PUBLIC_ROOT needed — uses default storage/app/public
```

**Production (.env):**
```env
FILESYSTEM_PUBLIC_ROOT=/home/u501912770/domains/laurensiusdimas.com/public_html/public/storage
```

This bypasses the symlink requirement by writing directly to `public/storage/`.

### FileUpload Components — CRITICAL
All Filament `FileUpload` components MUST specify disk and visibility:
```php
Forms\Components\FileUpload::make('logo_url')
    ->disk('public')           // ← REQUIRED
    ->visibility('public')     // ← REQUIRED  
    ->directory('clients')
```
Without these, files upload to private storage and won't be accessible.

### First-Time Production Setup
A one-time setup script creates necessary folders:
1. Upload `public/setup-storage.php` via FTP
2. Visit: `https://yourdomain.com/setup-storage.php?token=ld-setup-2025-delete-after-use`
3. **Delete the script immediately after**

### Deployment Workflow

1. **Build assets locally** (Hostinger has no npm):
   ```bash
   npm run build
   ```

2. **Commit built assets** (`public/build/` is NOT gitignored)

3. **Push to GitHub** then pull on server via FTP or Git

4. **Files to upload via FTP only** (not Git for security):
   - `.htaccess` (root)
   - `public/.htaccess`
   - `.env` (production version)

### Production .env Required Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://laurensiusdimas.com

DB_CONNECTION=mysql
DB_HOST=your-host
DB_DATABASE=your-db
DB_USERNAME=your-user
DB_PASSWORD=your-password

FILESYSTEM_PUBLIC_ROOT=/home/u501912770/domains/laurensiusdimas.com/public_html/public/storage
QUEUE_CONNECTION=sync
```

---

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

## CSS Marquee Pattern (Infinite Carousel)

For infinite scrolling carousels (clients section), use the responsive CSS-only technique:
```css
.marquee {
    --gap: 1.5rem;
    display: flex;
    overflow: hidden;
    user-select: none;
    gap: var(--gap);
}
.marquee__content {
    flex-shrink: 0;
    display: flex;
    justify-content: space-around;
    min-width: 100%;
    gap: var(--gap);
    animation: marquee-scroll 20s linear infinite;
}
@keyframes marquee-scroll {
    from { transform: translateX(0); }
    to { transform: translateX(calc(-100% - var(--gap))); }
}
```
Key: The `calc(-100% - var(--gap))` ensures seamless looping with dynamic content widths.

## Security Notes

- **Never commit `.env` files** — they're in `.gitignore`
- **Never commit credentials** — `docs/*` is gitignored
- `.htaccess` files uploaded via FTP only (not in repo)
- Use `git filter-repo` if secrets are accidentally committed
