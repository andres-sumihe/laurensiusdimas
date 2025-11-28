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

        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[90vw] sm:max-w-none">
            @php
                $isImage = preg_match('/\.(gif|png|jpe?g|webp|svg)$/i', $heroBlobUrl);
            @endphp
            @if($isImage)
                <img
                    src="{{ $heroBlobUrl }}"
                    alt="Animated blob"
                    class="max-h-[280px] sm:max-h-[380px] md:max-h-[450px] lg:max-h-[520px] w-auto mx-auto opacity-90 saturate-[1.15] brightness-[1.25] contrast-[1.05]"
                >
            @else
                <video
                    class="max-h-[280px] sm:max-h-[380px] md:max-h-[450px] lg:max-h-[520px] w-auto mx-auto opacity-90 saturate-[1.15] brightness-[1.25] contrast-[1.05]"
                    autoplay muted loop playsinline
                >
                    <source src="{{ $heroBlobUrl }}" type="video/mp4">
                </video>
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/20 to-black/30"></div>
        </div>

        <div class="relative z-10 text-left px-4 sm:px-6 lg:px-8">
            {{-- Hero title uses fluid typography: clamp(min, preferred, max) --}}
            <h1 class="font-display uppercase drop-shadow-[0_8px_28px_rgba(0,0,0,0.65)]" style="font-size: clamp(2rem, 5vw + 1rem, 4.5rem);">
                {{ $heroTitle }}
            </h1>
            <p class="font-mono text-sm sm:text-base md:text-lg lg:text-xl tracking-[0.08em] sm:tracking-[0.1em] text-white mt-2 sm:mt-3">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    {{-- ========================================
         ABOUT SECTION
    ========================================= --}}
    @if($bioShort || $bioLong || $logoUrl)
    <section class="bg-black text-white px-4 sm:px-6 md:px-8 py-12 sm:py-16 md:py-20">
        <div class="w-full mx-auto max-w-[1100px]">
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mb-6 sm:mb-8">
                <span class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-white whitespace-nowrap">
                    VISUAL WORKER
                </span>
                <div class="h-1 w-full sm:flex-1 bg-white"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                <div class="hidden md:flex md:col-span-4 my-auto justify-center items-center order-1">
                    @php
                        $logoUrl = $settings->favicon_url
                            ? (str_starts_with($settings->favicon_url, 'http')
                                ? $settings->favicon_url
                                : Storage::url($settings->favicon_url))
                            : asset('logo.svg');
                        $isSvg = pathinfo($logoUrl, PATHINFO_EXTENSION) === 'svg';
                    @endphp
                    @if($isSvg && file_exists(public_path('logo.svg')))
                        <span class="w-20 h-20 sm:w-[100px] sm:h-[100px] md:w-[120px] md:h-[120px]"> 
                            {!! file_get_contents(public_path('logo.svg')) !!}
                        </span>
                    @endif
                </div>

                {{-- Text Content (Right) --}}
                <div class="{{ $logoUrl ? 'md:col-span-8' : 'md:col-span-12' }} space-y-6 sm:space-y-8 order-2">
                    {{-- Bio --}}
                    <div class="space-y-6 text-gray-300 leading-relaxed">
                        @if($bioShort)
                            <p class="text-base md:text-lg font-bold text-white">
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
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-2">
                        @if($settings->resume_url)
                            <a
                                href="{{ str_starts_with($settings->resume_url, 'http') ? $settings->resume_url : Storage::url($settings->resume_url) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-white text-black text-sm font-extrabold tracking-wide hover:bg-gray-200 transition-colors"
                            >
                                <span>RESUME</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>

                        @else 
                            <a href="#start-a-project" class="group inline-flex items-center gap-2 text-sm font-semibold text-white transition-colors">
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
    <section class="bg-white text-black px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16">
        <div class="w-full mx-auto max-w-[1100px]">
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mb-8 sm:mb-10 md:mb-12">
                <span class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-black whitespace-nowrap">
                    {{ $portfolioHeading }}
                </span>
                <div class="h-1 w-full sm:flex-1 bg-black"></div>
            </div>

            {{-- Projects Grid --}}
            <div class="flex flex-col gap-8 sm:gap-10 md:gap-12">
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
    <section class="bg-white text-black px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16 relative overflow-hidden">
        <div class="w-full mx-auto max-w-[1100px]">
            {{-- Section Header --}}
            <div class="mb-2">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8 mb-2">
                    <h2 class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-black whitespace-nowrap">
                        CORPORATE PROJECTS
                    </h2>
                    <div class="h-1 w-full sm:flex-1 bg-black"></div>
                </div>
                <p class="font-body font-bold text-sm sm:text-base md:text-lg lg:text-xl uppercase tracking-widest text-black">
                    ESCO LIFESCIENCES GROUP
                </p>
            </div>

            {{-- Two-Row Infinite Marquee System --}}
            <div class="space-y-4 sm:space-y-6 mt-8 sm:mt-10 md:mt-12 relative">
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
                    {{-- Navigation Arrows - Hidden on mobile, visible on larger screens --}}
                    <button class="hidden sm:block absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="hidden sm:block absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>

                    {{-- Fade overlay left --}}
                    <div class="absolute -left-1 sm:-left-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-r from-white to-transparent"></div>
                    {{-- Fade overlay right --}}
                    <div class="absolute -right-1 sm:-right-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-l from-white to-transparent"></div>
                    
                    {{-- Scrollable Area - Landscape --}}
                    <div class="overflow-hidden">
                        <div class="marquee-landscape-track">
                            @php 
                                // Filter only landscape projects
                                $landscapeList = $corporateProjects->filter(fn($p) => $p->layout === 'landscape');
                            @endphp
                            {{-- First set --}}
                            <div class="flex gap-3 sm:gap-4 md:gap-6 shrink-0">
                                @foreach($landscapeList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer w-[280px] h-[143px] sm:w-[340px] sm:h-[174px] md:w-[420px] md:h-[215px]">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- Duplicate set for seamless loop --}}
                            <div class="flex gap-3 sm:gap-4 md:gap-6 shrink-0 ml-3 sm:ml-4 md:ml-6">
                                @foreach($landscapeList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer w-[280px] h-[143px] sm:w-[340px] sm:h-[174px] md:w-[420px] md:h-[215px]">
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
                    {{-- Navigation Arrows - Hidden on mobile, visible on larger screens --}}
                    <button class="hidden sm:block absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="hidden sm:block absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                        <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>

                    {{-- Fade overlay left --}}
                    <div class="absolute -left-1 sm:-left-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-r from-white to-transparent"></div>
                    {{-- Fade overlay right --}}
                    <div class="absolute -right-1 sm:-right-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-l from-white to-transparent"></div>
                    
                    {{-- Scrollable Area - Portrait --}}
                    <div class="overflow-hidden">
                        <div class="marquee-portrait-track">
                            @php 
                                // Filter only portrait projects
                                $portraitList = $corporateProjects->filter(fn($p) => $p->layout === 'portrait');
                            @endphp
                            {{-- First set --}}
                            <div class="flex gap-3 sm:gap-4 shrink-0">
                                @foreach($portraitList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer w-[150px] h-[273px] sm:w-[180px] sm:h-[327px] md:w-[220px] md:h-[400px]">
                                        @if($mediaUrl)
                                            <img src="{{ $mediaUrl }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- Duplicate set for seamless loop --}}
                            <div class="flex gap-3 sm:gap-4 shrink-0 ml-3 sm:ml-4">
                                @foreach($portraitList as $project)
                                    @php
                                        $firstMedia = collect($project->media_items ?? [])->first();
                                        $mediaUrl = $firstMedia ? (str_starts_with($firstMedia['url'] ?? '', 'http') ? $firstMedia['url'] : Storage::url($firstMedia['url'] ?? '')) : null;
                                    @endphp
                                    <div class="shrink-0 bg-gray-300 relative overflow-hidden cursor-pointer w-[150px] h-[273px] sm:w-[180px] sm:h-[327px] md:w-[220px] md:h-[400px]">
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
    <section class="bg-white text-black px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16  mb-[-180px] sm:mb-[-260px] md:mb-[-360px]">
        <div class="w-full mx-auto max-w-[1100px]">
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 mb-2">
                <h2 class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-black whitespace-nowrap">
                    OLDER PROJECTS
                </h2>
                <span class="font-body font-bold text-base sm:text-lg md:text-xl uppercase tracking-widest text-black">
                    {{ $olderYearRange }}
                </span>
                <div class="h-1 w-full sm:flex-1 bg-black"></div>
            </div>

            {{-- YouTube Embed Container --}}
            <div class="relative bg-[#d9d9d9] mt-6 sm:mt-8 md:mt-10 w-full aspect-video sm:aspect-[856/418]">
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
                        <p class="font-body font-bold text-xl sm:text-2xl md:text-3xl lg:text-4xl tracking-[0.2em] sm:tracking-[0.3em] text-gray-500 uppercase text-center px-4">
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
    <section class="bg-black text-white px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16 pt-[200px] sm:pt-[280px] md:pt-[380px]">
        <div class="w-full mx-auto max-w-[1100px]">
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8 mb-8 sm:mb-10 md:mb-12">
                <h2 class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-white whitespace-nowrap">
                    CLIENTS
                </h2>
                <div class="h-1 w-full sm:flex-1 bg-white"></div>
            </div>
            
            {{-- Endless Carousel Slider --}}
            <div class="relative overflow-hidden min-h-20 sm:min-h-[100px] md:min-h-[120px] flex items-center">
                {{-- Fade overlay left --}}
                <div class="absolute left-0 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-[120px] bg-gradient-to-r from-black to-transparent"></div>
                {{-- Fade overlay right --}}
                <div class="absolute right-0 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-[120px] bg-gradient-to-l from-black to-transparent"></div>
                
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
                    <div class="flex gap-6 sm:gap-8 md:gap-12 shrink-0">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center min-w-[120px] sm:min-w-[150px] md:min-w-[180px] h-[60px] sm:h-[80px] md:h-[100px]">
                                    <img 
                                        src="{{ $clientLogoUrl }}" 
                                        alt="{{ $client->name }}" 
                                        class="h-8 sm:h-12 md:h-16 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity grayscale hover:grayscale-0"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                        @endforeach
                    </div>
                    {{-- Duplicate set for seamless loop --}}
                    <div class="flex gap-6 sm:gap-8 md:gap-12 shrink-0 ml-6 sm:ml-8 md:ml-12">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center min-w-[120px] sm:min-w-[150px] md:min-w-[180px] h-[60px] sm:h-[80px] md:h-[100px]">
                                    <img 
                                        src="{{ $clientLogoUrl }}" 
                                        alt="{{ $client->name }}" 
                                        class="h-8 sm:h-12 md:h-16 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity grayscale hover:grayscale-0"
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
                <div class="flex items-center justify-center min-h-[150px] sm:min-h-[180px] md:min-h-[200px]">
                    <p class="font-body font-bold text-xl sm:text-2xl md:text-3xl lg:text-4xl tracking-[0.2em] sm:tracking-[0.3em] text-gray-500 uppercase">
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
    <footer class="bg-black text-white pb-10 sm:pb-12 md:pb-16 px-4 sm:px-6 md:px-8">
        <div class="w-full mx-auto max-w-[1100px]">
            
            {{-- CTA Section --}}
            <div class="flex flex-col gap-6 sm:gap-8 mb-12 sm:mb-16 md:mb-20">
                <div class="flex flex-col lg:flex-row items-start lg:items-center w-full gap-4 sm:gap-6 lg:gap-12">
                    <h2 class="font-body font-bold text-lg sm:text-xl md:text-2xl lg:text-3xl text-white">
                        Bring Your Vision to Life!
                    </h2>

                    <a id="start-a-project" href="mailto:{{ $contactEmail }}" class="w-full lg:flex-1 inline-flex items-center justify-center font-body font-bold px-6 sm:px-8 py-3 border border-white bg-transparent text-white text-base sm:text-lg rounded-xl transition-all hover:bg-white hover:text-black">
                        Start a Project
                    </a>
                </div>
            </div>

            {{-- Bottom Footer --}}
            <div class="flex flex-col sm:flex-row justify-between gap-6 text-sm border-t border-gray-800 pt-6 sm:pt-8">
                {{-- Logo + Copyright --}}
                <div class="flex sm:flex-row items-center gap-6 text-center sm:text-left">
                    <div class="hidden md:block">
                        @if($isSvg)
                            {{-- Inline SVG logo --}}
                            @php
                                $svgContent = null;
                                if (file_exists(public_path('logo.svg'))) {
                                    $svgContent = file_get_contents(public_path('logo.svg'));
                                    // remove any existing width/height attrs
                                    $svgContent = preg_replace('/\s*(width|height)="[^"]*"/i', '', $svgContent);
                                    // inject desired size + Tailwind classes
                                    $svgContent = preg_replace('/<svg([^>]*)>/i', '<svg$1 class="w-8 h-8" aria-hidden="true">', $svgContent, 1);
                                }
                            @endphp
    
                            @if($svgContent)
                                {!! $svgContent !!}
                            @endif
                        @endif
                    </div>
                    <p class="text-sm sm:text-base text-gray-400 text-left">
                        © {{ date('Y') }} {{ $heroTitle }} - {{ $heroSubtitle }}
                    </p>
                </div>
                
                {{-- Social Links --}}
                <div class="flex flex-wrap items-center justify-start gap-4 sm:gap-6 md:gap-8">
                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Email</a>
                    @endif
                    <a href="https://instagram.com" target="_blank" class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Instagram</a>
                    <a href="https://behance.net" target="_blank" class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Behance</a>
                    <a href="https://linkedin.com" target="_blank" class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>
</div>
