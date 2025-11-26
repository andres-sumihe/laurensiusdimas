# Technical Documentation

## 1. System Architecture
The application will be built as a monolithic PHP application using **Laravel 11**.

### 1.1 Routing Structure (Laravel Routing)
*   **Public Zone (`/`):** Public-facing pages rendered via Blade Templates & Livewire components.
*   **Admin Zone (`/admin`):** Protected routes managed by **FilamentPHP**.
*   **Routing Logic:** Standard Laravel `web.php` routes.

### 1.2 Data Flow
*   **Frontend:** Server-side rendering (SSR) with Blade. Dynamic interactivity provided by **Livewire** (server-driven) and **Alpine.js** (client-side).
*   **Admin:** **Filament Resources** handle CRUD operations, validation, and media management automatically.

## 2. Authentication & Security
*   **Admin Auth:** Managed by FilamentPHP (built on Laravel Guard).
*   **Session Management:** Standard PHP/Laravel file or database sessions (compatible with shared hosting).

## 3. Technical Constraints & Tech Stack
The development must strictly adhere to the following technologies:

### 3.1 Core Stack
*   **Framework:** **Laravel 12.12.0** (PHP 8.3).
*   **Frontend:** **Blade** + **Livewire v3** + **Alpine.js** (The TALL Stack).
*   **Admin Panel:** **FilamentPHP v3** (For rapid, premium dashboard development).
*   **Styling:** **Tailwind CSS**.

### 3.2 Backend & Data Layer
*   **Database:** **MySQL 5.7** (Hostinger Standard).
*   **ORM:** **Eloquent ORM** (Laravel's built-in active record implementation).
*   **Media Library:** **Spatie Media Library** (integrated with Filament).

### 3.3 Infrastructure & Media
*   **Media Storage:** **Local Filesystem** (`public/storage` linked to `storage/app/public`).
*   **Deployment:** **Hostinger Shared Hosting** (FTP/Git deployment).
    *   *Path:* `/domains/laurensiusdimas.com/public_html`
    *   *Requirement:* PHP 8.3, MySQL 5.7, Composer support.

### 3.4 Development Standards
*   **Type Safety:** PHP Type Hinting & Static Analysis (PHPStan/Pint).
*   **Single Repo Strategy:** Monolithic structure following standard Laravel conventions (`app/`, `resources/`, `routes/`).

### 3.3 Media & Performance
*   **Video Handling:**
    *   *Constraint:* Must implement **Lazy Loading** or Viewport observation (Intersection Observer) to only play videos when they are on screen to prevent memory leaks and high bandwidth usage.
*   **Image Optimization:** Use standard Vite asset handling or an external CDN.

## 4. Data Model

### 4.1 Project Entry (Collection: `projects`)
Primary content for the portfolio grid.
*   `id` (UUID): Unique identifier.
*   `slug` (String): URL-friendly identifier (unique).
*   `title` (String): Project name (e.g., "Nike Air Max").
*   `subtitle` (String): Category or short description (e.g., "Motion Design").
*   `description` (Text, Markdown): Full project details.
*   `isVisible` (Boolean): Draft/Published state.
*   `sortOrder` (Integer): For manual ordering in the grid.
*   `mediaItems` (JSON): Stored as JSON string in MySQL.
    *   `type`: "image" | "video"
    *   `url`: String (Local path e.g., `/uploads/project-1/image.jpg`)
    *   `thumbnailUrl`: String (Poster image for videos)
    *   `width`: Integer
    *   `height`: Integer
*   `createdAt` (Timestamp)
*   `updatedAt` (Timestamp)

### 4.2 Client Entry (Collection: `clients`)
For the "Trusted By" / Client logo ticker.
*   `id` (UUID)
*   `name` (String)
*   `logoUrl` (String): Local path e.g., `/uploads/clients/logo.png`.
*   `websiteUrl` (String, Optional): Link to client site.
*   `sortOrder` (Integer)
*   `isVisible` (Boolean)

### 4.3 Site Configuration (Singleton: `siteConfig`)
Global settings manageable via CMS.
*   **General & SEO:**
    *   `siteTitle` (String)
    *   `siteDescription` (String)
    *   `faviconUrl` (String)
    *   `ogImageUrl` (String)
*   **Hero Section:**
    *   `heroVideoUrl` (String)
    *   `heroHeadline` (String)
    *   `heroSubheadline` (String)
*   **Profile:**
    *   `profilePictureUrl` (String)
    *   `bioShort` (Text)
    *   `bioLong` (Text, Markdown)
    *   `resumeUrl` (String)
*   **Contact:**
    *   `email` (String)
    *   `socialLinks` (JSON): Stored as JSON string in MySQL. `[{ platform: "Instagram", url: "...", icon: "..." }]`
