<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $siteSettings = \App\Models\SiteSetting::current();
        $metaTitle = $title ?? $siteSettings->site_title ?? config('app.name');
        $metaDescription = $description ?? $siteSettings->site_description ?? 'Portfolio';
        $metaOgImage = $ogImage
            ?? ($siteSettings->og_image_url
                ? (str_starts_with($siteSettings->og_image_url, 'http')
                    ? $siteSettings->og_image_url
                    : \Illuminate\Support\Facades\Storage::url($siteSettings->og_image_url))
                : asset('images/og-default.jpg'));
        $favicon = $siteSettings->favicon_url
            ? (str_starts_with($siteSettings->favicon_url, 'http')
                ? $siteSettings->favicon_url
                : \Illuminate\Support\Facades\Storage::url($siteSettings->favicon_url))
            : asset('favicon.ico');
    @endphp

    <title>{{ $metaTitle }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaOgImage }}">
    <meta name="theme-color" content="#0b0c10">

    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif
    {{-- SVG favicon fallback using project logo (modern browsers) --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    {{-- Fallback to /favicon.ico for legacy browsers --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts: Montserrat (body) + Space Mono (monospace) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- CSS Variables for Fonts -->
    <style>
        :root {
            --font-display: 'Horizon', 'Bebas Neue', 'Oswald', sans-serif;
            --font-body: 'Montserrat', sans-serif;
            --font-mono: 'Space Mono', monospace;
        }
        
        /* Horizon Font */
        @font-face {
            font-family: 'Horizon';
            src: url('/fonts/Horizon.woff2') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        
        /* Font utility classes */
        .font-display, .font-horizon {
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .font-body, .font-montserrat {
            font-family: var(--font-body);
        }
        
        .font-mono, .font-spacemono {
            font-family: var(--font-mono);
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-neutral-950 text-white font-body">
    {{ $slot }}
    
    {{-- Lightbox Modal for Media Preview --}}
    <div 
        x-data="{ 
            open: false, 
            url: '', 
            type: 'image',
            youtubeId: null,
            
            // Extract YouTube video ID from various URL formats
            extractYouTubeId(url) {
                if (!url) return null;
                const patterns = [
                    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/,
                    /^([a-zA-Z0-9_-]{11})$/
                ];
                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) return match[1];
                }
                return null;
            },
            
            openLightbox(data) {
                this.url = data.url;
                this.type = data.type;
                
                // Auto-detect YouTube if type is youtube or URL is a YouTube link
                if (this.type === 'youtube' || this.extractYouTubeId(this.url)) {
                    this.youtubeId = this.extractYouTubeId(this.url);
                    this.type = 'youtube';
                } else {
                    this.youtubeId = null;
                }
                
                this.open = true;
                
                // Set YouTube iframe src after DOM update to trigger autoplay
                this.$nextTick(() => {
                    if (this.type === 'youtube' && this.youtubeId && this.$refs.youtubePlayer) {
                        // Full parameters to hide recommendations and branding
                        this.$refs.youtubePlayer.src = 'https://www.youtube.com/embed/' + this.youtubeId + '?autoplay=1&rel=0&modestbranding=1&playsinline=1&iv_load_policy=3&cc_load_policy=0&origin=' + encodeURIComponent(window.location.origin);
                    }
                    if (this.type === 'video' && this.$refs.videoPlayer) {
                        this.$refs.videoPlayer.play();
                    }
                });
            },
            
            close() { 
                this.open = false; 
                this.url = ''; 
                this.youtubeId = null;
                // Clear YouTube iframe to stop video
                if (this.$refs.youtubePlayer) {
                    this.$refs.youtubePlayer.src = '';
                }
                // Pause video
                if (this.$refs.videoPlayer) {
                    this.$refs.videoPlayer.pause();
                }
            }
        }"
        @open-lightbox.window="openLightbox($event.detail)"
        @keydown.escape.window="close()"
    >
        {{-- Backdrop --}}
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 z-[999] bg-black/90 backdrop-blur-sm"
            x-cloak
        ></div>
        
        {{-- Modal Content --}}
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 sm:p-8"
            @click.self="close()"
            x-cloak
        >
            {{-- Close Button --}}
            <button 
                @click="close()"
                class="absolute top-4 right-4 sm:top-8 sm:right-8 z-[1001] p-2 text-white/70 hover:text-white transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            {{-- Media Container --}}
            <div class="w-full h-full flex items-center justify-center p-4 sm:p-8">
                {{-- Image --}}
                <img 
                    x-show="type === 'image'"
                    x-bind:src="url"
                    alt="Preview"
                    class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                >
                
                {{-- Video --}}
                <video 
                    x-show="type === 'video'"
                    x-bind:src="url"
                    x-ref="videoPlayer"
                    controls
                    class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                >
                    Your browser does not support the video tag.
                </video>
                
                {{-- YouTube Embed - Full Size --}}
                <div 
                    x-show="type === 'youtube' && youtubeId"
                    class="w-full h-full max-w-[90vw] max-h-[85vh] flex items-center justify-center"
                >
                    <div class="w-full aspect-video max-h-[85vh]" style="max-width: min(90vw, calc(85vh * 16 / 9));">
                        <iframe 
                            x-ref="youtubePlayer"
                            class="w-full h-full rounded-xl shadow-2xl"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
