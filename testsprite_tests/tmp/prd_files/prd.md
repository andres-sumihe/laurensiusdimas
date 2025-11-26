# Product Requirements Document (PRD)

**Project Name:** Creative Portfolio & Integrated CMS
**Architecture:** Monolithic / Single Repository
**Tech Stack:** TanStack Start + Vite
**Version:** 1.3
**Status:** Draft

## 1. Executive Summary
The objective is to build a unified web application that serves as both a high-performance public portfolio for a creative professional and a private administration dashboard for content management. The project will utilize a modern, type-safe React architecture to ensure fast load times, seamless routing, and a unified developer experience within a single repository.

## 2. System Architecture
> [!NOTE]
> For detailed technical architecture, routing, and data flow, please refer to [technical-documentation.md](./technical-documentation.md).

## 3. Authentication & Security
> [!NOTE]
> Technical implementation details for authentication and security are documented in [technical-documentation.md](./technical-documentation.md).

## 4. Functional Requirements: Admin Dashboard (CMS)

### 4.1 Dashboard Overview
*   **Stats at a Glance:** Display total projects (Published/Draft), total clients, and quick links to add new content.
*   **Recent Activity:** List of recently modified projects or settings.

### 4.2 Project Management (Portfolio)
*   **List View:**
    *   Table view with columns: Thumbnail, Title, Category, Status (Published/Draft), Last Updated, Actions (Edit, Delete, Preview).
    *   Search and Filter functionality (by status, category).
    *   Drag-and-drop reordering to control the display order on the public site.
*   **Create/Edit Interface:**
    *   **General Info:** Title, Subtitle (Category), Slug (auto-generated but editable).
    *   **Rich Text Editor:** Markdown or WYSIWYG editor for the project description.
    *   **Media Gallery:**
        *   Multi-file upload (Images & Videos).
        *   Drag-and-drop reordering of media items.
        *   Video support: Auto-play preview, thumbnail selection.
    *   **SEO Settings:** Custom Meta Title, Meta Description, and OG Image for each project.
    *   **Visibility Control:** Toggle between "Draft" (hidden) and "Published" (visible).

### 4.3 Client Management (Trusted By)
*   **Client List:**
    *   Visual grid or list of client logos.
    *   Drag-and-drop reordering for the carousel sequence.
*   **Add/Edit Client:**
    *   Client Name.
    *   Logo Upload (SVG/PNG transparency support).
    *   Website URL (optional link).
    *   Visibility Toggle.

### 4.4 Global Site Settings
A centralized configuration area to manage the site's core content without code changes.
*   **General & SEO:**
    *   Site Title & Description (Default Meta tags).
    *   Favicon & Touch Icon upload.
    *   Social Sharing Image (Default OG Image).
*   **Dashboard Overview:** List view of all projects with status indicators (Published/Hidden).
*   **CRUD Operations:**
    *   **Create/Edit:** Form inputs for Title, Subtitle, and Description.
    *   **Media Management:** Upload interface for multiple Photos/Videos.
    *   **Delete Project:** Permanent removal of project and assets.
    *   **Visibility:** Toggle "Hide/Show" state (Draft vs. Published).
*   **Optimistic UI:** The dashboard should reflect changes immediately for a snappy experience.

## 5. Functional Requirements: Public Frontend
*   **Landing Experience:**
    *   Full-screen Video/GIF loop intro.
    *   Scroll-triggered reveal of the main project list.
*   **Project Gallery:**
    *   Responsive grid layout.
    *   Media cards must maintain a consistent rectangular aspect ratio.
*   **Video Behavior:**
    *   Videos auto-play and loop on load.
    *   *Performance Requirement:* Must implement strict Lazy Loading.
*   **Media Interaction:** Lightbox/Modal for viewing high-res media (Photo/Video).
*   **Client Carousel:** Auto-scrolling horizontal ticker for brand logos.
*   **Footer/Contact:**
    *   Profile bio.
    *   CTA buttons for WhatsApp and Email.
    *   Custom Favicon.

## 6. Technical Constraints & Tech Stack
The development must strictly adhere to the following technologies:

### 6.1 Core Stack
*   **Framework:** **Laravel 12.12.0** (PHP 8.3).
*   **Frontend:** **Blade** + **Livewire v3** + **Alpine.js** (The TALL Stack).
*   **Admin Panel:** **FilamentPHP v3** (For rapid, premium dashboard development).
*   **Styling:** **Tailwind CSS**.

### 6.2 Development Standards
*   **Type Safety:** PHP Type Hinting & Static Analysis (PHPStan/Pint).
*   **Single Repo Strategy:** Monolithic structure following standard Laravel conventions (`app/`, `resources/`, `routes/`).

## 7. Data Model
> [!NOTE]
> The detailed data schema including field types and relationships is available in [technical-documentation.md](./technical-documentation.md).

### 7.1 Core Entities
*   **Projects:** The main portfolio items containing media, titles, and descriptions.
*   **Clients:** Logos for the "Trusted By" section.
*   **Site Config:** Global settings for hero video, bio, and contact info.

## 8. UI/UX References
* **Canva:** [canva reference](https://laurensiusdimas.my.canva.site/)
* **Rifqi Art Studio:** [rifqiartstudio.com](https://rifqiartstudio.com)
* **VSLZM:** [vslzm.com](https://vslzm.com)