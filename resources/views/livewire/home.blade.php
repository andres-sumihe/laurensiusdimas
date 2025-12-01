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
    
@endphp

<div class="min-h-screen max-w-[1440px] mx-auto bg-black text-white">
    {{-- ========================================
         HERO SECTION - Full Width Background (breaks out of container)
    ========================================= --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden 
                    -mx-[calc((100vw-100%)/2)] w-[100vw] max-w-[100vw]">
        <div class="absolute inset-0 bg-black"></div>

        {{-- Header Logo - Top Left (positioned relative to 1440px container) --}}
        <div class="reveal-hero absolute top-4 left-4 sm:top-8 sm:left-8 md:top-10 md:left-12 lg:top-[12%] z-20
                    lg:left-[calc((100vw-1440px)/2+48px)]">
            <a href="#" class="block">
                <img 
                    src="{{ asset('logo.svg') }}" 
                    alt="LD Logo" 
                    class="w-8 h-auto sm:w-10 md:w-12 lg:w-[54px] drop-shadow-lg"
                >
            </a>
        </div>

        {{-- Hero Background Media - Full viewport coverage --}}
        <div class="pointer-events-none absolute inset-0 w-full h-full">
            @php
                $isVideo = preg_match('/\.(mp4|webm|mov)$/i', $heroBlobUrl);
                $isGif = preg_match('/\.gif$/i', $heroBlobUrl);
                $isImage = preg_match('/\.(png|jpe?g|webp|svg)$/i', $heroBlobUrl);
                $isFullscreen = $isVideo || $isImage; // Fullscreen for video and static images, NOT for GIF
            @endphp
            @if($isVideo)
                {{-- Fullscreen video background --}}
                <video
                    class="w-full h-full object-cover opacity-80"
                    autoplay muted loop playsinline
                >
                    <source src="{{ $heroBlobUrl }}" type="video/mp4">
                </video>
            @elseif($isGif)
                {{-- Centered blob style for GIF --}}
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
                    <img
                        src="{{ $heroBlobUrl }}"
                        alt="Animated blob"
                        class="max-h-[280px] sm:max-h-[380px] md:max-h-[450px] lg:max-h-[520px] w-auto mx-auto opacity-90 saturate-[1.15] brightness-[1.25] contrast-[1.05]"
                    >
                </div>
            @else
                {{-- Fullscreen image background for static images --}}
                <img
                    src="{{ $heroBlobUrl }}"
                    alt="Hero background"
                    class="w-full h-full object-cover opacity-80"
                >
            @endif
            {{-- Gradient overlays for text readability and section transition --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent"></div>
            {{-- Bottom fade to black - seamless transition to next section --}}
            <div class="absolute inset-x-0 bottom-0 h-[30%] bg-gradient-to-b from-transparent to-black"></div>
        </div>

        <div class="relative z-10 text-left mx-4">
            {{-- Hero title uses fluid typography: clamp(min, preferred, max) --}}
            <h1 class="reveal-hero font-display uppercase drop-shadow-[0_8px_28px_rgba(0,0,0,0.65)]" style="font-size: clamp(2rem, 5vw + 1rem, 48px);">
                {{ $heroTitle }}
            </h1>
            <p class="reveal-hero reveal-delay-200 font-mono text-[12px] sm:text-base md:text-lg lg:text-[18px] tracking-[0.08em] sm:tracking-widest text-white mt-2 sm:mt-3">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    {{-- ========================================
         ABOUT SECTION
    ========================================= --}}
    @if($bioShort || $bioLong || $logoUrl)
    <section class="bg-black text-white px-4 sm:px-6 md:px-8 py-12 sm:py-16 md:py-20">
        <div class="w-full mx-auto max-w-[1200px]">
            {{-- Section Header --}}
            <div class="reveal-fade-up flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mb-6 sm:mb-8">
                <span class="font-display text-md sm:text-[28px] md:text-[32px] uppercase text-white whitespace-nowrap">
                    VISUAL WORKER
                </span>
                <div class="h-1 w-full sm:flex-1 bg-white"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                <div class="reveal-scale reveal-delay-200 hidden md:flex md:col-span-4 my-auto justify-center items-center order-1">
                    @php
                        $aboutLogoUrl = asset('logo.svg');
                    @endphp
                    <img 
                        src="{{ $aboutLogoUrl }}" 
                        alt="Logo" 
                        class="w-20 h-20 sm:w-[100px] sm:h-[100px] md:w-[120px] md:h-[120px] object-contain"
                    >
                </div>

                {{-- Text Content (Right) --}}
                <div class="reveal-fade-up reveal-delay-100 {{ $logoUrl ? 'md:col-span-8' : 'md:col-span-12' }} space-y-6 sm:space-y-8 order-2">
                    {{-- Bio --}}
                    <div class="space-y-6 text-gray-300 leading-relaxed">
                        @if($bioShort)
                            <p class="text-[12px] md:text-lg font-bold text-white">
                                {{ $bioShort }}
                            </p>
                        @endif
                        
                        @if($bioLong)
                            <div class="text-[12px] md:text-base text-gray-400">
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
                                class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-white text-black text-[12px] font-extrabold tracking-wide hover:bg-gray-200 transition-colors"
                            >
                                <span>RESUME</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>

                        @else 
                            <a href="#start-a-project" class="group inline-flex items-center gap-2 text-[12px] font-semibold text-white transition-colors">
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
    <section class="bg-white text-black px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16 rounded-t-4xl">
        <div class="w-full mx-auto max-w-[1200px]">
            {{-- Section Header --}}
            <div class="reveal-fade-up flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mb-8 sm:mb-10 md:mb-12">
                <span class="font-display text-md sm:text-[28px] md:text-[32px] uppercase text-black whitespace-nowrap">
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
        <div class="w-full mx-auto max-w-[1200px]">
            {{-- Section Header --}}
            <div class="reveal-fade-up mb-2">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8 mb-2">
                    <h2 class="font-display text-md sm:text-[28px] md:text-[32px] uppercase text-black whitespace-nowrap">
                        CORPORATE PROJECTS
                    </h2>
                    <div class="h-1 w-full sm:flex-1 bg-black"></div>
                </div>
            </div>

            {{-- CSS for Marquee Animation --}}
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

            {{-- Loop through each client's projects --}}
            @foreach($corporateProjects as $clientId => $clientProjects)
                @php
                    $client = $clientProjects->first()->client ?? null;
                    $clientName = $client->name ?? 'Unknown Client';
                    
                    // Helper function to extract YouTube video ID
                    $extractYouTubeId = function($url) {
                        if (!$url) return null;
                        $patterns = [
                            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
                            '/^([a-zA-Z0-9_-]{11})$/'
                        ];
                        foreach ($patterns as $pattern) {
                            if (preg_match($pattern, $url, $matches)) {
                                return $matches[1];
                            }
                        }
                        return null;
                    };
                    
                    // Collect all corporate media from all projects for this client
                    $allMedia = $clientProjects->flatMap(fn($p) => $p->corporateMedia ?? collect());
                    $landscapeMedia = $allMedia->filter(fn($m) => $m->layout === 'landscape')->sortBy('sort_order');
                    $portraitMedia = $allMedia->filter(fn($m) => $m->layout === 'portrait')->sortBy('sort_order');
                @endphp
                
                <div class="{{ !$loop->first ? 'mt-12 sm:mt-16' : '' }}">
                    {{-- Client Name --}}
                    <p class="font-body font-bold text-[12px] sm:text-base md:text-lg lg:text-xl uppercase tracking-widest text-black mb-6 sm:mb-8">
                        {{ $clientName }}
                    </p>

                    {{-- Two-Row Infinite Marquee System --}}
                    <div class="space-y-4 sm:space-y-6 relative">
                        {{-- Top Row: Landscape Images --}}
                        @if($landscapeMedia->count() > 0)
                        <div class="relative">
                            {{-- Navigation Arrows --}}
                            <button class="hidden sm:block absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                                <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button class="hidden sm:block absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                                <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                            </button>

                            {{-- Fade overlays --}}
                            <div class="absolute -left-1 sm:-left-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-r from-white to-transparent"></div>
                            <div class="absolute -right-1 sm:-right-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-l from-white to-transparent"></div>
                            
                            {{-- Scrollable Area - Landscape --}}
                            <div class="overflow-hidden">
                                <div class="marquee-landscape-track">
                                    {{-- First set --}}
                                    <div class="flex gap-3 sm:gap-4 md:gap-6 shrink-0">
                                        @foreach($landscapeMedia as $media)
                                            @php
                                                $mediaUrl = $media->url ? (str_starts_with($media->url, 'http') ? $media->url : Storage::url($media->url)) : null;
                                                $mediaType = $media->type ?? 'image';
                                                $thumbnailUrl = $media->thumbnail_url ? (str_starts_with($media->thumbnail_url, 'http') ? $media->thumbnail_url : Storage::url($media->thumbnail_url)) : null;
                                                $youtubeId = null;
                                                
                                                // Check for YouTube
                                                if ($mediaType === 'youtube' || str_contains($media->url ?? '', 'youtube') || str_contains($media->url ?? '', 'youtu.be')) {
                                                    $youtubeId = $extractYouTubeId($media->url);
                                                    if ($youtubeId) {
                                                        $mediaType = 'youtube';
                                                        $thumbnailUrl = $thumbnailUrl ?? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
                                                    }
                                                }
                                            @endphp
                                            <div class="shrink-0 bg-gray-300 relative overflow-hidden w-[280px] h-[143px] sm:w-[340px] sm:h-[174px] md:w-[420px] md:h-[215px]">
                                                @if($mediaType === 'youtube' && $youtubeId)
                                                    @include('livewire.partials.youtube-inline-player', [
                                                        'youtubeId' => $youtubeId,
                                                        'thumbnailUrl' => $thumbnailUrl,
                                                        'uniqueId' => 'corp-land-' . $media->id,
                                                        'showControls' => true,
                                                    ])
                                                @elseif($mediaUrl)
                                                    <div class="w-full h-full cursor-pointer" @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })">
                                                        @if($mediaType === 'video')
                                                            <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" alt="Corporate media" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- Duplicate set for seamless loop --}}
                                    <div class="flex gap-3 sm:gap-4 md:gap-6 shrink-0 ml-3 sm:ml-4 md:ml-6">
                                        @foreach($landscapeMedia as $media)
                                            @php
                                                $mediaUrl = $media->url ? (str_starts_with($media->url, 'http') ? $media->url : Storage::url($media->url)) : null;
                                                $mediaType = $media->type ?? 'image';
                                                $thumbnailUrl = $media->thumbnail_url ? (str_starts_with($media->thumbnail_url, 'http') ? $media->thumbnail_url : Storage::url($media->thumbnail_url)) : null;
                                                $youtubeId = null;
                                                
                                                // Check for YouTube
                                                if ($mediaType === 'youtube' || str_contains($media->url ?? '', 'youtube') || str_contains($media->url ?? '', 'youtu.be')) {
                                                    $youtubeId = $extractYouTubeId($media->url);
                                                    if ($youtubeId) {
                                                        $mediaType = 'youtube';
                                                        $thumbnailUrl = $thumbnailUrl ?? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
                                                    }
                                                }
                                            @endphp
                                            <div class="shrink-0 bg-gray-300 relative overflow-hidden w-[280px] h-[143px] sm:w-[340px] sm:h-[174px] md:w-[420px] md:h-[215px]">
                                                @if($mediaType === 'youtube' && $youtubeId)
                                                    @include('livewire.partials.youtube-inline-player', [
                                                        'youtubeId' => $youtubeId,
                                                        'thumbnailUrl' => $thumbnailUrl,
                                                        'uniqueId' => 'corp-land-dup-' . $media->id,
                                                        'showControls' => true,
                                                    ])
                                                @elseif($mediaUrl)
                                                    <div class="w-full h-full cursor-pointer" @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })">
                                                        @if($mediaType === 'video')
                                                            <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" alt="Corporate media" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Bottom Row: Portrait Images --}}
                        @if($portraitMedia->count() > 0)
                        <div class="relative">
                            {{-- Navigation Arrows --}}
                            <button class="hidden sm:block absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                                <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button class="hidden sm:block absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 bg-white/90 hover:bg-white rounded-full p-1.5 sm:p-2 shadow-lg text-black hover:text-gray-600 transition-all">
                                <svg width="20" height="20" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                            </button>

                            {{-- Fade overlays --}}
                            <div class="absolute -left-1 sm:-left-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-r from-white to-transparent"></div>
                            <div class="absolute -right-1 sm:-right-2.5 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-24 bg-gradient-to-l from-white to-transparent"></div>
                            
                            {{-- Scrollable Area - Portrait --}}
                            <div class="overflow-hidden">
                                <div class="marquee-portrait-track">
                                    {{-- First set --}}
                                    <div class="flex gap-3 sm:gap-4 shrink-0">
                                        @foreach($portraitMedia as $media)
                                            @php
                                                $mediaUrl = $media->url ? (str_starts_with($media->url, 'http') ? $media->url : Storage::url($media->url)) : null;
                                                $mediaType = $media->type ?? 'image';
                                                $thumbnailUrl = $media->thumbnail_url ? (str_starts_with($media->thumbnail_url, 'http') ? $media->thumbnail_url : Storage::url($media->thumbnail_url)) : null;
                                                $youtubeId = null;
                                                
                                                // Check for YouTube
                                                if ($mediaType === 'youtube' || str_contains($media->url ?? '', 'youtube') || str_contains($media->url ?? '', 'youtu.be')) {
                                                    $youtubeId = $extractYouTubeId($media->url);
                                                    if ($youtubeId) {
                                                        $mediaType = 'youtube';
                                                        $thumbnailUrl = $thumbnailUrl ?? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
                                                    }
                                                }
                                            @endphp
                                            <div class="shrink-0 bg-gray-300 relative overflow-hidden w-[150px] h-[273px] sm:w-[180px] sm:h-[327px] md:w-[220px] md:h-[400px]">
                                                @if($mediaType === 'youtube' && $youtubeId)
                                                    @include('livewire.partials.youtube-inline-player', [
                                                        'youtubeId' => $youtubeId,
                                                        'thumbnailUrl' => $thumbnailUrl,
                                                        'uniqueId' => 'corp-port-' . $media->id,
                                                        'showControls' => true,
                                                    ])
                                                @elseif($mediaUrl)
                                                    <div class="w-full h-full cursor-pointer" @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })">
                                                        @if($mediaType === 'video')
                                                            <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" alt="Corporate media" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- Duplicate set for seamless loop --}}
                                    <div class="flex gap-3 sm:gap-4 shrink-0 ml-3 sm:ml-4">
                                        @foreach($portraitMedia as $media)
                                            @php
                                                $mediaUrl = $media->url ? (str_starts_with($media->url, 'http') ? $media->url : Storage::url($media->url)) : null;
                                                $mediaType = $media->type ?? 'image';
                                                $thumbnailUrl = $media->thumbnail_url ? (str_starts_with($media->thumbnail_url, 'http') ? $media->thumbnail_url : Storage::url($media->thumbnail_url)) : null;
                                                $youtubeId = null;
                                                
                                                // Check for YouTube
                                                if ($mediaType === 'youtube' || str_contains($media->url ?? '', 'youtube') || str_contains($media->url ?? '', 'youtu.be')) {
                                                    $youtubeId = $extractYouTubeId($media->url);
                                                    if ($youtubeId) {
                                                        $mediaType = 'youtube';
                                                        $thumbnailUrl = $thumbnailUrl ?? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
                                                    }
                                                }
                                            @endphp
                                            <div class="shrink-0 bg-gray-300 relative overflow-hidden w-[150px] h-[273px] sm:w-[180px] sm:h-[327px] md:w-[220px] md:h-[400px]">
                                                @if($mediaType === 'youtube' && $youtubeId)
                                                    @include('livewire.partials.youtube-inline-player', [
                                                        'youtubeId' => $youtubeId,
                                                        'thumbnailUrl' => $thumbnailUrl,
                                                        'uniqueId' => 'corp-port-dup-' . $media->id,
                                                        'showControls' => true,
                                                    ])
                                                @elseif($mediaUrl)
                                                    <div class="w-full h-full cursor-pointer" @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })">
                                                        @if($mediaType === 'video')
                                                            <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" alt="Corporate media" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ========================================
         OLDER PROJECTS (Single Hero Cards)
    ========================================= --}}
    @if($olderProjects->count() > 0)
    <section class="bg-white text-black px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16 rounded-b-4xl">
        <div class="w-full mx-auto max-w-[1200px] mb-[-180px] sm:mb-[-360px]">
            {{-- Section Header --}}
            <div class="reveal-fade-up flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 mb-8 sm:mb-10 md:mb-12">
                <h2 class="font-display text-md sm:text-[28px] md:text-[32px] uppercase text-black whitespace-nowrap">
                    {{ $olderHeading }}
                </h2>
                <span class="font-body font-bold text-base sm:text-lg md:text-xl uppercase tracking-widest text-black">
                    {{ $olderYearRange }}
                </span>
                <div class="h-1 w-full sm:flex-1 bg-black"></div>
            </div>

            {{-- Older Projects Grid (Simple Media Cards) --}}
            <div class="flex flex-col gap-8 sm:gap-10 md:gap-12">
                @foreach($olderProjects as $project)
                    @php
                        // Get the first media item for this project
                        $media = $project->projectMedia->first();
                        $mediaUrl = null;
                        $mediaType = 'image';
                        $thumbnailUrl = null;
                        $youtubeId = null;
                        
                        if ($media) {
                            $mediaType = $media->type ?? 'image';
                            $mediaUrl = $media->url;
                            $thumbnailUrl = $media->thumbnail_url;
                            
                            // Extract YouTube ID if it's a YouTube video
                            if ($mediaType === 'youtube' || (str_contains($mediaUrl ?? '', 'youtube') || str_contains($mediaUrl ?? '', 'youtu.be'))) {
                                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $mediaUrl, $matches);
                                $youtubeId = $matches[1] ?? null;
                                if ($youtubeId) {
                                    $mediaType = 'youtube';
                                    $thumbnailUrl = $thumbnailUrl ?: "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
                                }
                            }
                            
                            // Build full URL for local files
                            if ($mediaUrl && !str_starts_with($mediaUrl, 'http') && $mediaType !== 'youtube') {
                                $mediaUrl = Storage::url($mediaUrl);
                            }
                            if ($thumbnailUrl && !str_starts_with($thumbnailUrl, 'http')) {
                                $thumbnailUrl = Storage::url($thumbnailUrl);
                            }
                        }
                    @endphp
                    
                    @if($media)
                        {{-- YouTube Video with Inline Autoplay on Scroll --}}
                        @if($mediaType === 'youtube' && $youtubeId)
                            <div 
                                x-data="{ 
                                    inView: false, 
                                    hasPlayed: false,
                                    isMuted: true,
                                    isPaused: false,
                                    youtubeId: '{{ $youtubeId }}',
                                    player: null,
                                    playerId: 'yt-player-{{ $youtubeId }}-{{ $loop->index }}',
                                    thumbnailUrl: '{{ $thumbnailUrl }}',
                                    
                                    init() {
                                        // Load YouTube IFrame API if not already loaded
                                        if (!window.YT) {
                                            const tag = document.createElement('script');
                                            tag.src = 'https://www.youtube.com/iframe_api';
                                            const firstScriptTag = document.getElementsByTagName('script')[0];
                                            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                                        }
                                        
                                        const observer = new IntersectionObserver((entries) => {
                                            entries.forEach(entry => {
                                                if (entry.isIntersecting && !this.hasPlayed) {
                                                    this.inView = true;
                                                    this.hasPlayed = true;
                                                    this.loadPlayer();
                                                }
                                            });
                                        }, { threshold: 0.5 });
                                        observer.observe(this.$el);
                                    },
                                    
                                    loadPlayer() {
                                        const checkYT = setInterval(() => {
                                            if (window.YT && window.YT.Player) {
                                                clearInterval(checkYT);
                                                this.player = new YT.Player(this.playerId, {
                                                    videoId: this.youtubeId,
                                                    playerVars: {
                                                        autoplay: 1,
                                                        mute: 1,
                                                        loop: 1,
                                                        playlist: this.youtubeId,
                                                        controls: 0,
                                                        showinfo: 0,
                                                        rel: 0,
                                                        modestbranding: 1,
                                                        playsinline: 1,
                                                        fs: 0,
                                                        iv_load_policy: 3,
                                                        disablekb: 1,
                                                        cc_load_policy: 0,
                                                        origin: window.location.origin
                                                    },
                                                    events: {
                                                        onReady: (event) => {
                                                            event.target.playVideo();
                                                        },
                                                        onStateChange: (event) => {
                                                            // Track paused state to show overlay
                                                            if (event.data === YT.PlayerState.PAUSED) {
                                                                this.isPaused = true;
                                                            } else if (event.data === YT.PlayerState.PLAYING) {
                                                                this.isPaused = false;
                                                            }
                                                            // When video ends, restart immediately
                                                            if (event.data === YT.PlayerState.ENDED) {
                                                                event.target.seekTo(0);
                                                                event.target.playVideo();
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                        }, 100);
                                    },
                                    
                                    toggleMute() {
                                        if (this.player && this.player.isMuted) {
                                            if (this.player.isMuted()) {
                                                this.player.unMute();
                                                this.isMuted = false;
                                            } else {
                                                this.player.mute();
                                                this.isMuted = true;
                                            }
                                        }
                                    },
                                    
                                    togglePlay() {
                                        if (this.player) {
                                            if (this.isPaused) {
                                                this.player.playVideo();
                                            } else {
                                                this.player.pauseVideo();
                                            }
                                        }
                                    }
                                }"
                                class="relative w-full aspect-video rounded-3xl overflow-hidden group"
                            >
                                {{-- Thumbnail (shown before video loads) --}}
                                <img 
                                    x-show="!inView"
                                    src="{{ $thumbnailUrl }}"
                                    alt="{{ $project->title }}"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    loading="lazy"
                                />
                                
                                {{-- YouTube Player Container --}}
                                <div 
                                    x-show="inView"
                                    :id="playerId"
                                    class="absolute inset-0 w-full h-full"
                                ></div>
                                
                                {{-- Control Buttons --}}
                                <div class="absolute bottom-4 right-4 flex items-center gap-2 z-20">
                                    {{-- Play/Pause Button --}}
                                    <button 
                                        x-show="inView"
                                        @click.stop="togglePlay()"
                                        class="bg-black/70 hover:bg-black/90 text-white rounded-full p-2.5 transition-all transform hover:scale-110 backdrop-blur-sm"
                                        :title="isPaused ? 'Play' : 'Pause'"
                                    >
                                        {{-- Play Icon --}}
                                        <svg x-show="isPaused" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        {{-- Pause Icon --}}
                                        <svg x-show="!isPaused" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                                        </svg>
                                    </button>
                                    
                                    {{-- Mute/Unmute Button --}}
                                    <button 
                                        x-show="inView"
                                        @click.stop="toggleMute()"
                                        class="bg-black/70 hover:bg-black/90 text-white rounded-full p-2.5 transition-all transform hover:scale-110 backdrop-blur-sm"
                                        :title="isMuted ? 'Unmute' : 'Mute'"
                                    >
                                        {{-- Muted Icon --}}
                                        <svg x-show="isMuted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                                        </svg>
                                        {{-- Unmuted Icon --}}
                                        <svg x-show="!isMuted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                        </svg>
                                    </button>
                                    
                                    {{-- Fullscreen Button --}}
                                    <button 
                                        @click.stop="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: 'youtube' })"
                                        class="bg-black/70 hover:bg-black/90 text-white rounded-full p-2.5 transition-all transform hover:scale-110 backdrop-blur-sm"
                                        title="Fullscreen"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                {{-- Gradient overlay at bottom for button visibility --}}
                                <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/50 to-transparent pointer-events-none"></div>
                            </div>
                        {{-- Regular Video --}}
                        @elseif($mediaType === 'video')
                            <div 
                                class="relative w-full aspect-video rounded-3xl overflow-hidden cursor-pointer group"
                                @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })"
                            >
                                <video 
                                    src="{{ $mediaUrl }}"
                                    poster="{{ $thumbnailUrl }}"
                                    class="w-full h-full object-cover"
                                    muted
                                    loop
                                    playsinline
                                    onmouseenter="this.play()"
                                    onmouseleave="this.pause()"
                                ></video>
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                            </div>
                        {{-- Image --}}
                        @else
                            <div 
                                class="relative w-full aspect-video rounded-3xl overflow-hidden cursor-pointer group"
                                @click="$dispatch('open-lightbox', { url: '{{ $mediaUrl }}', type: '{{ $mediaType }}' })"
                            >
                                <img 
                                    src="{{ $mediaUrl }}"
                                    alt="{{ $project->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         CLIENTS SECTION (Black Background) - Endless Carousel
    ========================================= --}}
    @if($clients->count() > 0)
    <section class="bg-black text-white px-4 sm:px-6 md:px-8 py-10 sm:py-12 md:py-16 pt-[200px] sm:pt-[280px] md:pt-[380px]">
        <div class="w-full mx-auto max-w-[1200px]">
            {{-- Section Header --}}
            <div class="reveal-fade-up flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8 mb-8 sm:mb-10 md:mb-12">
                <h2 class="font-display text-md sm:text-[28px] md:text-[32px] uppercase text-white whitespace-nowrap">
                    CLIENTS
                </h2>
                <div class="h-1 w-full sm:flex-1 bg-white"></div>
            </div>
            
            {{-- Endless Carousel Slider --}}
            <div class="reveal-fade reveal-delay-200 relative overflow-hidden min-h-20 sm:min-h-[100px] md:min-h-[120px] flex items-center">
                {{-- Fade overlay left --}}
                <div class="absolute left-0 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-[120px] bg-gradient-to-r from-black to-transparent"></div>
                {{-- Fade overlay right --}}
                <div class="absolute right-0 top-0 bottom-0 z-10 pointer-events-none w-12 sm:w-20 md:w-[120px] bg-gradient-to-l from-black to-transparent"></div>
                
                {{-- Carousel container with animation --}}
                @php
                    $clientsList = $clients->filter(fn($c) => !empty($c->logo_url));
                    $clientCount = $clientsList->count();
                    $animationDuration = max(20, $clientCount * 4);
                @endphp
                
                <style>
                    .marquee-clients {
                        --gap: 1.5rem;
                        display: flex;
                        overflow: hidden;
                        user-select: none;
                        gap: var(--gap);
                    }
                    @media (min-width: 640px) {
                        .marquee-clients { --gap: 2rem; }
                    }
                    @media (min-width: 768px) {
                        .marquee-clients { --gap: 3rem; }
                    }
                    .marquee-clients__content {
                        flex-shrink: 0;
                        display: flex;
                        justify-content: space-around;
                        min-width: 100%;
                        gap: var(--gap);
                        animation: marquee-clients-scroll {{ $animationDuration }}s linear infinite;
                    }
                    .marquee-clients:hover .marquee-clients__content {
                        animation-play-state: paused;
                    }
                    @keyframes marquee-clients-scroll {
                        from { transform: translateX(0); }
                        to { transform: translateX(calc(-100% - var(--gap))); }
                    }
                </style>
                
                <div class="marquee-clients">
                    {{-- First set --}}
                    <div class="marquee-clients__content">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center h-[60px] sm:h-20 md:h-[100px]">
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
                    <div class="marquee-clients__content" aria-hidden="true">
                        @foreach($clientsList as $client)
                            @php
                                $clientLogoUrl = $client->logo_url 
                                    ? (str_starts_with($client->logo_url, 'http') 
                                        ? $client->logo_url 
                                        : Storage::url($client->logo_url)) 
                                    : null;
                            @endphp
                            @if($clientLogoUrl)
                                <div class="shrink-0 flex items-center justify-center h-[60px] sm:h-20 md:h-[100px]">
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
        <div class="w-full mx-auto max-w-[1200px]">
            
            {{-- CTA Section --}}
            <div class="reveal-fade-up flex flex-col gap-6 sm:gap-8 mb-12 sm:mb-16 md:mb-20">
                <div class="flex flex-col lg:flex-row items-start lg:items-center w-full gap-4 sm:gap-6 lg:gap-12">
                    <h2 class="font-body font-bold text-md sm:text-xl md:text-2xl lg:text-3xl text-white">
                        Bring Your Vision to Life!
                    </h2>

                    <a id="start-a-project" href="mailto:{{ $contactEmail }}" class="w-full lg:flex-1 inline-flex items-center justify-center font-body font-bold px-6 sm:px-8 py-3 border border-white bg-transparent text-white text-base sm:text-lg rounded-xl transition-all hover:bg-white hover:text-black">
                        Start a Project
                    </a>
                </div>
            </div>

            {{-- Bottom Footer --}}
            <div class="reveal-fade reveal-delay-200 flex flex-col sm:flex-row justify-between gap-6 text-sm border-t border-gray-800 pt-6 sm:pt-8">
                {{-- Logo + Copyright --}}
                <div class="flex sm:flex-row items-center gap-6 text-center sm:text-left">
                    <div class="hidden md:block">
                        <img 
                            src="{{ asset('logo.svg') }}" 
                            alt="Logo" 
                            class="w-8 h-8 object-contain"
                        >
                    </div>
                    <p class="text-[12px] sm:text-base text-gray-400 text-left">
                        © {{ date('Y') }} {{ $heroTitle }} - {{ $heroSubtitle }}
                    </p>
                </div>
                
                {{-- Social Links --}}
                <div class="flex flex-wrap items-center justify-start gap-4 sm:gap-6 md:gap-8">
                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" class="text-[12px] sm:text-base text-gray-400 hover:text-white transition-colors">Email</a>
                    @endif
                    @forelse($socialLinks as $link)
                        @if(!empty($link['url']) && !empty($link['platform']))
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-[12px] sm:text-base text-gray-400 hover:text-white transition-colors">
                                {{ $link['platform'] }}
                            </a>
                        @endif
                    @empty
                        {{-- Fallback links if no social links configured --}}
                        <a href="https://instagram.com" target="_blank" class="text-[12px] sm:text-base text-gray-400 hover:text-white transition-colors">Instagram</a>
                        <a href="https://behance.net" target="_blank" class="text-[12px] sm:text-base text-gray-400 hover:text-white transition-colors">Behance</a>
                        <a href="https://linkedin.com" target="_blank" class="text-[12px] sm:text-base text-gray-400 hover:text-white transition-colors">LinkedIn</a>
                    @endforelse
                </div>
            </div>
        </div>
    </footer>
</div>
