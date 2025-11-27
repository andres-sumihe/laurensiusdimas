@php
    use Illuminate\Support\Facades\Storage;
    
    $portfolioHeading = $settings->portfolio_heading ?? 'CURATED PROJECTS';
    $olderHeading = $settings->older_heading ?? 'OLDER PROJECTS';
    $corporateHeading = $settings->corporate_heading ?? 'CORPORATE PROJECTS';
    $corporateSubheading = $settings->corporate_subheading ?? '';
    
    // Build older year range string
    $olderYearFrom = $settings->older_year_from ?? 2019;
    $olderYearTo = $settings->older_year_to ?? 2023;
    $olderYearRange = $olderYearFrom . '-' . $olderYearTo;

    // Hero blob - animated GIF from Canva (default)
    $heroBlobDefault = 'https://laurensiusdimas.my.canva.site/_assets/video/c35e66dcc624edcf5432e90a8bc96345.gif';
    $heroBlobUrl = $settings->hero_video_url
        ? (str_starts_with($settings->hero_video_url, 'http')
            ? $settings->hero_video_url
            : Storage::url($settings->hero_video_url))
        : $heroBlobDefault;

    $heroTitle = strtoupper($settings->hero_headline ?? 'LAURENSIUS DIMAS');
    $heroSubtitle = $settings->hero_subheadline ?? 'VFX Enthusiast  |  3D Generalist  |  Sound Design';

    // Logo / Contact — prefer site logo, fallback to profile picture
    $logoUrl = $settings->logo_url
        ? (str_starts_with($settings->logo_url, 'http')
            ? $settings->logo_url
            : Storage::url($settings->logo_url))
        : (str_starts_with($settings->profile_picture_url ?? '', 'http')
            ? ($settings->profile_picture_url ?? null)
            : ($settings->profile_picture_url ? Storage::url($settings->profile_picture_url) : asset('logo.svg')));
    $bioShort = $settings->bio_short ?? null;
    $bioLong = $settings->bio_long ?? null;
    $contactEmail = $settings->email ?? null;
    $socialLinks = $settings->social_links ?? [];
    $footerText = $settings->footer_text ?? null;
    $footerCtaLabel = $settings->footer_cta_label ?? null;
    $footerCtaUrl = $settings->footer_cta_url ?? null;
    
    // Corporate Projects
    $corporateProjects = \App\Models\Project::where('is_visible', true)
        ->where('section', 'corporate')
        ->orderBy('sort_order')
        ->get();
@endphp

<div class="min-h-screen bg-black text-white">
    {{-- ========================================
         HERO SECTION - Animated Blob Background
    ========================================= --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-black"></div>

        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            @php
                $isImage = preg_match('/\.(gif|png|jpe?g|webp|svg)$/i', $heroBlobUrl);
            @endphp
            @if($isImage)
                <img
                    src="{{ $heroBlobUrl }}"
                    alt="Animated blob"
                    class="max-h-[520px] w-auto opacity-90 saturate-[1.15] brightness-[1.25] contrast-[1.05]"
                >
            @else
                <video
                    class="max-h-[520px] w-auto opacity-90 saturate-[1.15] brightness-[1.25] contrast-[1.05]"
                    autoplay muted loop playsinline
                >
                    <source src="{{ $heroBlobUrl }}" type="video/mp4">
                </video>
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/20 to-black/30"></div>
        </div>

        <div class="relative z-10 text-left px-6">
            <h1 class="font-display text-5xl md:text-6xl lg:text-7xl xl:text-[96px] tracking-[0%] drop-shadow-[0_8px_28px_rgba(0,0,0,0.65)] uppercase">
                {{ $heroTitle }}
            </h1>
            <p class="font-mono text-[15px] xl:text-[28px] tracking-[13%] text-white">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    {{-- ========================================
         ABOUT SECTION - Enhanced Layout
    ========================================= --}}
    @if($bioShort || $bioLong || $logoUrl)
    <section class="bg-black text-white px-4 md:px-8 py-20">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px;">
                <span class="font-display" style="font-size: 64px; text-transform: uppercase; color: white; white-space: nowrap;">
                    VISUAL WORKER
                </span>
                <div style="height: 4px; flex: 1; background: white;"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-start">
                @php
                    $logoUrl = $settings->favicon_url
                        ? (str_starts_with($settings->favicon_url, 'http')
                            ? $settings->favicon_url
                            : Storage::url($settings->favicon_url))
                        : asset('logo.svg');
                    $isSvg = pathinfo($logoUrl, PATHINFO_EXTENSION) === 'svg';
                @endphp
                @if($isSvg && file_exists(public_path('logo.svg')))
                    <div class="flex col-span-4 my-auto md:w-auto justify-center items-center order-1 md:order-1">
                        <span class="w-[120px] h-[120px]"> 
                            {!! file_get_contents(public_path('logo.svg')) !!}
                        </span>
                    </div>
                @else
                    {{-- Fallback to image or placeholder --}}
                    <div class="flex col-span-4 my-auto md:w-auto justify-center items-center order-1 md:order-1">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="w-[120px] h-[120px] object-contain" />
                        @else
                            <span style="font-family: var(--font-display, serif); font-size: 14px; font-weight: 700; color: #000;">L</span>
                        @endif
                    </div>
                @endif

                {{-- Text Content (Right) --}}
                <div class="{{ $logoUrl ? 'md:col-span-8' : 'md:col-span-12' }} space-y-8 order-2 md:order-2">
                    
                    {{-- Bio --}}
                    <div class="space-y-6 text-gray-300 leading-relaxed">
                        @if($bioShort)
                            <p class="text-lg font-medium text-white">
                                {{ $bioShort }}
                            </p>
                        @endif
                        
                        @if($bioLong)
                            <div class="text-sm md:text-base text-gray-400">
                                {!! $bioLong !!}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Links --}}
                    <div class="flex flex-wrap items-center gap-6 pt-2">
                        @if($settings->resume_url)
                            <a
                                href="{{ str_starts_with($settings->resume_url, 'http') ? $settings->resume_url : Storage::url($settings->resume_url) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-black text-sm font-extrabold tracking-wide hover:bg-gray-200 transition-colors"
                            >
                                <span>RESUME</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>

                        @else 
                            <a href="#start-a-project" class="group inline-flex items-center gap-2 text-sm font-semibold text-whitetransition-colors">
                                <span>reach me out!</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         CURATED PROJECTS SECTION
    ========================================= --}}
    @if($curatedProjects->count() > 0)
    <section class="bg-white text-black px-4 md:px-8 py-16">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 48px;">
                <span class="font-display" style="font-size: 56px; text-transform: uppercase; color: #000; white-space: nowrap;">
                    {{ $portfolioHeading }}
                </span>
                <div style="height: 4px; flex: 1; background: #000;"></div>
            </div>

            {{-- Projects Grid --}}
            <div style="display: flex; flex-direction: column; gap: 48px;">
                @foreach($curatedProjects as $project)
                    @include('livewire.partials.project-card', ['project' => $project])
                @endforeach
            </div>
        </div>
    </section>
    @endif

        {{-- ========================================
         CORPORATE PROJECTS SECTION (Two-Row Carousel)
    ========================================= --}}
    @if($corporateProjects->count() > 0)
    <section class="bg-white text-black py-20 relative overflow-hidden">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            {{-- Section Header --}}
            <div class="mb-2">
                <div style="display: flex; align-items: center; gap: 32px; margin-bottom: 8px;">
                    <h2 class="font-display" style="font-size: 56px; text-transform: uppercase; color: #000; white-space: nowrap; margin: 0;">
                        CORPORATE PROJECTS
                    </h2>
                    <div style="height: 4px; flex: 1; background: #000;"></div>
                </div>
                <p class="font-body font-bold" style="font-size: 24px; text-transform: uppercase; letter-spacing: 3.6px; color: #000; margin: 0;">
                    ESCO LIFESCIENCES GROUP
                </p>
            </div>

            {{-- Two-Row Infinite Marquee System --}}
            <div class="space-y-6 mt-12 relative">
                <style>
                    @keyframes marquee-landscape {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }
                    @keyframes marquee-portrait {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }
                    .marquee-landscape-track {
                        display: flex;
                        width: max-content;
                        animation: marquee-landscape 30s linear infinite;
                    }
                    .marquee-landscape-track:hover {
                        animation-play-state: paused;
                    }
                    .marquee-portrait-track {
                        display: flex;
                        width: max-content;
                        animation: marquee-portrait 25s linear infinite;
                    }
                    .marquee-portrait-track:hover {
                        animation-play-state: paused;
                    }
                </style>

                {{-- Top Row: Landscape Images --}}
                <div class="relative">
                    {{-- Navigation Arrows - Always visible --}}
                    <button class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>

                    {{-- Fade overlay left --}}
                    <div class="absolute -left-2.5 top-0 bottom-0 z-10 pointer-events-none" style="width: 100px; background: linear-gradient(to right, rgba(255,255,255,1) 15%, rgba(255,255,255,0) 100%);"></div>
                    {{-- Fade overlay right --}}
                    <div class="absolute -right-2.5 top-0 bottom-0 z-10 pointer-events-none" style="width: 100px; background: linear-gradient(to left, rgba(255,255,255,1) 15%, rgba(255,255,255,0) 100%);"></div>
                    
                    {{-- Scrollable Area - Landscape --}}
                    <div class="overflow-hidden">
                        <div class="marquee-landscape-track">
                            @php 
                                // Filter only landscape projects
                                $landscapeList = $corporateProjects->filter(fn($p) => $p->layout === 'landscape');
                            @endphp
                            {{-- First set --}}
                            <div class="flex gap-6 shrink-0">
                                @foreach($landscapeList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer" style="width: 420px; height: 215px;">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- Duplicate set for seamless loop --}}
                            <div class="flex gap-6 shrink-0 ml-6">
                                @foreach($landscapeList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer" style="width: 420px; height: 215px;">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Row: Portrait Images --}}
                <div class="relative">
                    {{-- Navigation Arrows - Always visible --}}
                    <button class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>

                    {{-- Fade overlay left --}}
                    <div class="absolute -left-2.5 top-0 bottom-0 z-10 pointer-events-none" style="width: 100px; background: linear-gradient(to right, rgba(255,255,255,1) 15%, rgba(255,255,255,0) 100%);"></div>
                    {{-- Fade overlay right --}}
                    <div class="absolute -right-2.5 top-0 bottom-0 z-10 pointer-events-none" style="width: 100px; background: linear-gradient(to left, rgba(255,255,255,1) 15%, rgba(255,255,255,0) 100%);"></div>
                    
                    {{-- Scrollable Area - Portrait --}}
                    <div class="overflow-hidden">
                        <div class="marquee-portrait-track">
                            @php 
                                // Filter only portrait projects
                                $portraitList = $corporateProjects->filter(fn($p) => $p->layout === 'portrait');
                            @endphp
                            {{-- First set --}}
                            <div class="flex gap-4 shrink-0">
                                @foreach($portraitList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer" style="width: 220px; height: 400px;">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- Duplicate set for seamless loop --}}
                            <div class="flex gap-4 shrink-0 ml-4">
                                @foreach($portraitList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer" style="width: 220px; height: 400px;">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         OLDER PROJECTS (YouTube Embed Style)
    ========================================= --}}
    @if($olderProjects->count() > 0)
    <section class="bg-white text-black py-20 mb-[-360px]">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 8px;">
                <h2 class="font-display" style="font-size: 56px; text-transform: uppercase; color: #000; white-space: nowrap; margin: 0;">
                    OLDER PROJECTS
                </h2>
                <span class="font-body font-bold" style="font-size: 24px; text-transform: uppercase; letter-spacing: 3.6px; color: #000;">
                    {{ $olderYearRange }}
                </span>
                <div style="height: 4px; flex: 1; background: #000;"></div>
            </div>

            {{-- YouTube Embed Container --}}
            <div class="relative bg-[#d9d9d9] mt-10" style="width: 100%; aspect-ratio: 856/418;">
                @php
                    $firstOlderProject = $olderProjects->first();
                    $youtubeUrl = $firstOlderProject->youtube_url ?? null;
                @endphp
                @if($youtubeUrl)
                    <iframe 
                        class="absolute inset-0 w-full h-full" 
                        src="{{ $youtubeUrl }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <p class="font-body font-bold" style="font-size: 36px; letter-spacing: 10.8px; color: grey; text-transform: uppercase;">
                            EMBED YOUTUBE URL
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         CLIENTS SECTION (Black Background) - Endless Carousel
    ========================================= --}}
    @if($clients->count() > 0)
    <section class="bg-black text-white py-24 pt-[380px]">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 32px; margin-bottom: 48px;">
                <h2 class="font-display" style="font-size: 56px; text-transform: uppercase; color: #fff; white-space: nowrap; margin: 0;">
                    CLIENTS
                </h2>
                <div style="height: 4px; flex: 1; background: #fff;"></div>
            </div>
            
            {{-- Endless Carousel Slider --}}
            <div class="relative overflow-hidden min-h-[120px] flex items-center">
                {{-- Fade overlay left --}}
                <div class="absolute left-0 top-0 bottom-0 z-10 pointer-events-none" style="width: 120px; background: linear-gradient(to right, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);"></div>
                {{-- Fade overlay right --}}
                <div class="absolute right-0 top-0 bottom-0 z-10 pointer-events-none" style="width: 120px; background: linear-gradient(to left, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);"></div>
                
                {{-- Carousel container with animation --}}
                <style>
                    @keyframes marquee-clients {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }
                    .marquee-clients-track {
                        display: flex;
                        width: max-content;
                        animation: marquee-clients 15s linear infinite;
                    }
                    .marquee-clients-track:hover {
                        animation-play-state: paused;
                    }
                </style>
                
                <div class="marquee-clients-track">
                    @php
                        $clientsList = $clients->filter(fn($c) => !empty($c->logo_url));
                    @endphp
                    {{-- First set --}}
                    <div class="flex gap-12 shrink-0">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center" style="min-width: 180px; height: 100px;">
                                    <img 
                                        src="{{ $clientLogoUrl }}" 
                                        alt="{{ $client->name }}" 
                                        class="h-12 md:h-16 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity grayscale hover:grayscale-0"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                        @endforeach
                    </div>
                    {{-- Duplicate set for seamless loop --}}
                    <div class="flex gap-12 shrink-0 ml-12">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center" style="min-width: 180px; height: 100px;">
                                    <img 
                                        src="{{ $clientLogoUrl }}" 
                                        alt="{{ $client->name }}" 
                                        class="h-12 md:h-16 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity grayscale hover:grayscale-0"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Fallback message if no logos --}}
            @if($clients->every(fn($c) => empty($c->logo_url)))
                <div class="flex items-center justify-center min-h-[200px]">
                    <p class="font-body font-bold" style="font-size: 36px; letter-spacing: 10.8px; color: grey; text-transform: uppercase;">
                        CLIENTS LOGO
                    </p>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ========================================
         FOOTER
    ========================================= --}}
    <footer class="bg-black text-white pb-16">
        <div class="w-full mx-auto" style="max-width: 1200px;">
            
            {{-- CTA Section --}}
            <div class="flex flex-col w-full md:flex-row items-center mb-20">
                <div class="flex items-center w-full gap-16">
                    <h2 class="font-body font-bold whitespace-nowrap shrink-0" style="font-size: 32px; color: #fff; margin: 0;">
                        Bring Your Vision to Life!
                    </h2>

                    <a id="start-a-project" href="mailto:{{ $contactEmail }}" class="flex-1 inline-flex items-center justify-center font-body font-bold px-8 py-3 border border-white bg-transparent text-white text-lg rounded-xl transition-all" onmouseover="this.style.background='#fff'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='#fff';">
                        Start a Project
                    </a>
                </div>
            </div>

            {{-- Bottom Footer --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-sm border-t border-gray-800 pt-8">
                {{-- Logo + Copyright --}}
                <div class="flex items-center">
                    @if($isSvg)
                        {{-- Inline SVG logo --}}
                        @php
                            $svgContent = null;
                            if (file_exists(public_path('logo.svg'))) {
                                $svgContent = file_get_contents(public_path('logo.svg'));
                                // remove any existing width/height attrs
                                $svgContent = preg_replace('/\s*(width|height)="[^"]*"/i', '', $svgContent);
                                // inject desired size + Tailwind classes
                                $svgContent = preg_replace('/<svg([^>]*)>/i', '<svg$1 width="40" height="40" class="w-[4] h-4" aria-hidden="true">', $svgContent, 1);
                            }
                        @endphp

                        @if($svgContent)
                            {!! $svgContent !!}
                        @endif
                    @endif
                    <p style="font-family: var(--font-body, sans-serif); font-size: 16px; color: #999; margin: 0;">
                        © {{ date('Y') }} Laurensius Dimas - 3D Generalist & Motion Designer
                    </p>
                </div>
                
                {{-- Social Links --}}
                <div class="flex items-center gap-8">
                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" style="font-family: var(--font-body, sans-serif); font-size: 16px; color: #999; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#999'">Email</a>
                    @endif
                    <a href="https://instagram.com" target="_blank" style="font-family: var(--font-body, sans-serif); font-size: 16px; color: #999; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#999'">Instagram</a>
                    <a href="https://behance.net" target="_blank" style="font-family: var(--font-body, sans-serif); font-size: 16px; color: #999; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#999'">Behance</a>
                    <a href="https://linkedin.com" target="_blank" style="font-family: var(--font-body, sans-serif); font-size: 16px; color: #999; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#999'">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>
</div>
