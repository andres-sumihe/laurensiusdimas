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

    // Profile / Contact
    $profilePicUrl = $settings->profile_picture_url
        ? (str_starts_with($settings->profile_picture_url, 'http')
            ? $settings->profile_picture_url
            : Storage::url($settings->profile_picture_url))
        : null;
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

        <div class="relative z-10 text-left space-y-4 px-6">
            <h1 class="font-display text-5xl md:text-6xl lg:text-7xl xl:text-[92px] tracking-[0.05em] drop-shadow-[0_8px_28px_rgba(0,0,0,0.65)] uppercase">
                {{ $heroTitle }}
            </h1>
            <p class="text-[11px] md:text-sm tracking-[0.45em] text-gray-200 font-medium">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    {{-- ========================================
         ABOUT SECTION - Enhanced Layout
    ========================================= --}}
    @if($bioShort || $bioLong || $profilePicUrl)
    <section class="bg-white text-black px-4 md:px-8 py-20">
        <div class="w-full mx-auto" style="max-width: 760px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px;">
                <span style="font-family: var(--font-display, serif); font-size: clamp(24px, 4vw, 38px); text-transform: uppercase; letter-spacing: 0.08em; color: #4a4a4a; white-space: nowrap;">
                    About
                </span>
                <div style="height: 3px; flex: 1; background: #4a4a4a;"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-start">
                {{-- Profile Photo (Left) --}}
                @if($profilePicUrl)
                <div class="md:col-span-4 order-1 md:order-1">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gray-100 transform translate-x-2 translate-y-2 transition-transform duration-300 group-hover:translate-x-1 group-hover:translate-y-1"></div>
                        <img 
                            src="{{ $profilePicUrl }}" 
                            alt="Profile" 
                            class="relative w-full aspect-[3/4] object-cover grayscale group-hover:grayscale-0 transition-all duration-500 shadow-sm"
                            loading="lazy"
                        >
                    </div>
                </div>
                @endif

                {{-- Text Content (Right) --}}
                <div class="{{ $profilePicUrl ? 'md:col-span-8' : 'md:col-span-12' }} space-y-8 order-2 md:order-2">
                    
                    {{-- Bio --}}
                    <div class="space-y-6 text-gray-600 leading-relaxed">
                        @if($bioShort)
                            <p class="text-lg font-medium text-gray-800">
                                {{ $bioShort }}
                            </p>
                        @endif
                        
                        @if($bioLong)
                            <div class="text-sm md:text-base text-gray-500">
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
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#363439] text-white text-sm font-medium tracking-wide hover:bg-black transition-colors"
                            >
                                <span>RESUME</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        @endif
                        
                        @if($contactEmail)
                            <a href="mailto:{{ $contactEmail }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-black transition-colors">
                                <span>{{ $contactEmail }}</span>
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
        <div class="w-full mx-auto" style="max-width: 760px;">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 48px;">
                <span style="font-family: var(--font-display, serif); font-size: clamp(24px, 4vw, 38px); text-transform: uppercase; letter-spacing: 0.08em; color: #4a4a4a; white-space: nowrap;">
                    {{ $portfolioHeading }}
                </span>
                <div style="height: 3px; flex: 1; background: #4a4a4a;"></div>
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

    {{-- OLDER PROJECTS --}}
    @if($olderProjects->count() > 0)
    <section class="bg-white text-black px-4 md:px-8 py-14">
        <div class="w-full mx-auto" style="max-width: 760px;">
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 32px;">
                <span style="font-family: var(--font-display, serif); font-size: clamp(22px, 4vw, 36px); text-transform: uppercase; letter-spacing: 0.08em; color: #4a4a4a; white-space: nowrap;">
                    {{ $olderHeading }}
                </span>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #9ca3af;">
                    {{ $olderYearRange ?? '' }}
                </span>
                <div style="height: 3px; flex: 1; background: #4a4a4a;"></div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 36px;">
                @foreach($olderProjects as $project)
                    @include('livewire.partials.project-card', ['project' => $project])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CLIENTS / TRUSTED BY (Marquee with consistent header style) --}}
    @if($clients->count() > 0)
    <section class="bg-white text-black px-4 md:px-8 py-16">
        <div class="w-full mx-auto" style="max-width: 760px;">
            {{-- Section Header - Matching Curated/Older Projects style --}}
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 40px;">
                <span style="font-family: var(--font-display, serif); font-size: clamp(22px, 4vw, 36px); text-transform: uppercase; letter-spacing: 0.08em; color: #4a4a4a; white-space: nowrap;">
                    Trusted By
                </span>
                <div style="height: 3px; flex: 1; background: #4a4a4a;"></div>
            </div>
            
            {{-- Marquee Container --}}
            <div class="relative overflow-hidden py-4">
                {{-- Fade edges --}}
                <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-white via-white/80 to-transparent pointer-events-none z-10"></div>
                <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-white via-white/80 to-transparent pointer-events-none z-10"></div>
                
                {{-- Scrolling Track --}}
                <div class="flex items-center gap-16 whitespace-nowrap animate-marquee">
                    @foreach($clients as $client)
                        @php
                            $logoUrl = $client->logo_url
                                ? (str_starts_with($client->logo_url, 'http')
                                    ? $client->logo_url
                                    : Storage::url($client->logo_url))
                                : null;
                        @endphp
                        <div class="flex-shrink-0 flex items-center justify-center px-2">
                            @if($logoUrl)
                                <img 
                                    src="{{ $logoUrl }}" 
                                    alt="{{ $client->name }}" 
                                    style="height: 40px; width: auto; max-width: 120px;" 
                                    class="object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300" 
                                    loading="lazy"
                                >
                            @else
                                <span class="text-base font-semibold text-[#4a4a4a] opacity-50 hover:opacity-100 transition-opacity">{{ $client->name }}</span>
                            @endif
                        </div>
                    @endforeach
                    {{-- Duplicate for seamless loop --}}
                    @foreach($clients as $client)
                        @php
                            $logoUrl = $client->logo_url
                                ? (str_starts_with($client->logo_url, 'http')
                                    ? $client->logo_url
                                    : Storage::url($client->logo_url))
                                : null;
                        @endphp
                        <div class="flex-shrink-0 flex items-center justify-center px-2">
                            @if($logoUrl)
                                <img 
                                    src="{{ $logoUrl }}" 
                                    alt="{{ $client->name }}" 
                                    style="height: 40px; width: auto; max-width: 120px;" 
                                    class="object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300" 
                                    loading="lazy"
                                >
                            @else
                                <span class="text-base font-semibold text-[#4a4a4a] opacity-50 hover:opacity-100 transition-opacity">{{ $client->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- FOOTER (simple) --}}
    <footer class="bg-white text-black px-4 md:px-8 pb-12">
        <div class="w-full space-y-4" style="max-width: 760px; margin: 0 auto;">
            @if($footerText)
                <div class="text-sm text-gray-700 leading-relaxed">{{ $footerText }}</div>
            @endif
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm text-gray-700">
                <p>© {{ date('Y') }} {{ $settings->site_title ?? 'Laurensius Dimas' }}</p>
                <div class="flex flex-wrap gap-4">
                    @if($footerCtaLabel && $footerCtaUrl)
                        <a href="{{ $footerCtaUrl }}" target="_blank" class="font-semibold text-[#363439] underline">
                            {{ $footerCtaLabel }}
                        </a>
                    @endif
                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" class="text-gray-600 hover:text-black">Email</a>
                    @endif
                    @if($socialLinks && count($socialLinks) > 0)
                        @foreach($socialLinks as $link)
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-gray-600 hover:text-black"
                            >
                                {{ $link['platform'] }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </footer>
</div>
