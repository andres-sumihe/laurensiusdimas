@php
    $portfolioHeading = $settings->portfolio_heading ?? 'CURATED PROJECTS';
    $portfolioSubheading = $settings->portfolio_subheading ?? null;
    $corporateHeading = $settings->corporate_heading ?? 'CORPORATE PROJECTS';
    $corporateSubheading = $settings->corporate_subheading ?? 'ESCO LIFESCIENCES GROUP';
    $olderHeading = $settings->older_heading ?? 'OLDER PROJECTS';
    $olderSubheading = $settings->older_subheading ?? '2019-2023';

    $heroFluidDefault = 'https://laurensiusdimas.my.canva.site/_assets/video/c35e66dcc624edcf5432e90a8bc96345.gif';
    $heroMediaUrl = $settings->hero_video_url
        ? (str_starts_with($settings->hero_video_url, 'http')
            ? $settings->hero_video_url
            : Storage::url($settings->hero_video_url))
        : $heroFluidDefault;

    $heroTitle = strtoupper($settings->hero_headline ?? 'LAURENSIUS DIMAS');
    $heroSubtitle = 'VFX Enthusiast  |  3D Generalist  |  Sound Design';
    $profilePicture = $settings->profile_picture_url
        ? (str_starts_with($settings->profile_picture_url, 'http')
            ? $settings->profile_picture_url
            : Storage::url($settings->profile_picture_url))
        : null;
@endphp

<div class="min-h-screen bg-black text-white">
    {{-- Hero --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6">
        <div class="absolute inset-0 bg-black"></div>

        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            @if(str_ends_with($heroMediaUrl, '.gif'))
                <img
                    src="{{ $heroMediaUrl }}"
                    alt="Hero blob"
                    class="max-h-[520px] w-auto opacity-70 saturate-[0.7] brightness-[0.45]"
                    loading="lazy"
                >
            @else
                <video
                    class="max-h-[520px] w-auto opacity-70 saturate-[0.7] brightness-[0.45]"
                    autoplay
                    muted
                    loop
                    playsinline
                >
                    <source src="{{ $heroMediaUrl }}" type="video/mp4">
                </video>
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/45 to-black/60"></div>
        </div>

        <div class="relative z-10 text-center space-y-4">
            <h1 class="font-display text-5xl md:text-6xl lg:text-7xl xl:text-[92px] tracking-[0.05em] drop-shadow-[0_8px_28px_rgba(0,0,0,0.65)] uppercase">
                {{ $heroTitle }}
            </h1>
            <p class="text-[11px] md:text-sm tracking-[0.45em] text-gray-200 font-medium">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    {{-- Portfolio --}}
    <section class="bg-white text-black px-6 md:px-8 py-16">
        <div class="w-full space-y-10" style="max-width: 760px; margin: 0 auto;">
            <div class="flex items-center gap-6">
                <span class="font-display text-3xl md:text-4xl lg:text-[38px] uppercase tracking-[0.08em] text-[#4a4a4a]">
                    {{ $portfolioHeading }}
                </span>
                <div class="h-[3px] flex-1 bg-[#4a4a4a]"></div>
            </div>

            <div class="space-y-10">
                @foreach($projects as $project)
                    @php
                        $layoutRaw = $project->layout ?? 'three_two';
                        // Back-compat with previous layout names
                        $layout = match ($layoutRaw) {
                            'collage_3_2' => 'three_two',
                            'default' => 'single',
                            'full' => 'single',
                            'split' => 'two',
                            default => $layoutRaw,
                        };
                        $mediaItems = collect($project->media_items ?? [])->map(function ($item) {
                            $url = $item['url'] ?? null;
                            $thumb = $item['thumbnailUrl'] ?? null;
                            return [
                                'type' => $item['type'] ?? 'image',
                                'url' => $url ? (str_starts_with($url, 'http') ? $url : Storage::url($url)) : null,
                                'thumb' => $thumb ? (str_starts_with($thumb, 'http') ? $thumb : Storage::url($thumb)) : null,
                            ];
                        })->filter(fn ($m) => $m['url'])->values();

                        $gridClass = match ($layout) {
                            'three_two' => 'grid-cols-3 grid-rows-2',
                            'three_two_tall' => 'grid-cols-3 grid-rows-2',
                            'two' => 'grid-cols-2 grid-rows-1',
                            'single' => 'grid-cols-1 grid-rows-1',
                            default => 'grid-cols-3 grid-rows-2',
                        };

                        $gridHeight = match ($layout) {
                            'three_two' => 'h-[240px] md:h-[280px]',
                            'three_two_tall' => 'h-[280px] md:h-[340px]',
                            'two' => 'h-[240px] md:h-[300px]',
                            'single' => '',
                            default => 'h-[240px] md:h-[280px]',
                        };
                    @endphp

                    <article class="space-y-3">
                        @if($layout === 'single' || $mediaItems->count() <= 2)
                            @php $item = $mediaItems->first(); @endphp
                            @if($item)
                                <div class="w-full overflow-hidden bg-neutral-100">
                                    <div class="aspect-[16/9]">
                                        @if($item['type'] === 'video')
                                            <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                                                <source src="{{ $item['url'] }}" type="video/mp4">
                                            </video>
                                        @else
                                            <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="grid {{ $gridClass }} {{ $gridHeight }} gap-2 md:gap-3 w-full">
                                @foreach($mediaItems as $index => $item)
                                    @php
                                        // Explicit grid positioning for each layout
                                        $gridPos = '';
                                        if ($layout === 'three_two') {
                                            // First 3 items: col-span 2, row 1
                                            // Items 4-5: col-span 3, row 2
                                            if ($index === 0) $gridPos = 'col-start-1 col-end-3 row-start-1 row-end-2';
                                            elseif ($index === 1) $gridPos = 'col-start-3 col-end-5 row-start-1 row-end-2';
                                            elseif ($index === 2) $gridPos = 'col-start-5 col-end-7 row-start-1 row-end-2';
                                            elseif ($index === 3) $gridPos = 'col-start-1 col-end-4 row-start-2 row-end-3';
                                            elseif ($index === 4) $gridPos = 'col-start-4 col-end-7 row-start-2 row-end-3';
                                        } elseif ($layout === 'three_two_tall') {
                                            // First 3 items: col-span 2, row 1
                                            // Items 4-5: col-span 3, row 2 (taller)
                                            if ($index === 0) $gridPos = 'col-start-1 col-end-3 row-start-1 row-end-2';
                                            elseif ($index === 1) $gridPos = 'col-start-3 col-end-5 row-start-1 row-end-2';
                                            elseif ($index === 2) $gridPos = 'col-start-5 col-end-7 row-start-1 row-end-2';
                                            elseif ($index === 3) $gridPos = 'col-start-1 col-end-4 row-start-2 row-end-3';
                                            elseif ($index === 4) $gridPos = 'col-start-4 col-end-7 row-start-2 row-end-3';
                                        } elseif ($layout === 'two') {
                                            // 2 items side by side
                                            if ($index === 0) $gridPos = 'col-start-1 col-end-2 row-start-1 row-end-2';
                                            elseif ($index === 1) $gridPos = 'col-start-2 col-end-3 row-start-1 row-end-2';
                                        }
                                    @endphp
                                    <div class="overflow-hidden bg-neutral-100 {{ $gridPos }}">
                                        @if($item['type'] === 'video')
                                            <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                                                <source src="{{ $item['url'] }}" type="video/mp4">
                                            </video>
                                        @else
                                            <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="space-y-1">
                            <h3 class="font-display text-2xl md:text-3xl uppercase text-[#363439]">
                                {{ strtoupper($project->title) }}
                            </h3>
                            @if($project->subtitle)
                                <p class="text-[10px] uppercase tracking-[0.28em] text-gray-600">
                                    {{ $project->subtitle }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Corporate + Archive --}}
    @if($clients->count() > 0 || $corporateHeading || $olderHeading)
        <section class="bg-white text-black px-6 md:px-12 pb-32">
            <div class="max-w-[1140px] mx-auto">
                {{-- Corporate Section --}}
                <div class="mb-32">
                    <div class="flex flex-col md:flex-row md:items-end gap-4 mb-16 border-b border-black/10 pb-6">
                        <h2 class="font-display text-3xl md:text-4xl uppercase text-[#363439]">
                            {{ $corporateHeading }}
                        </h2>
                        @if($corporateSubheading)
                            <p class="text-sm font-bold uppercase tracking-widest text-gray-400 md:mb-1 md:ml-4">
                                {{ $corporateSubheading }}
                            </p>
                        @endif
                    </div>

                    @if($clients->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-20">
                            @foreach($clients as $index => $client)
                                @php
                                    $logo = $client->logo_url
                                        ? (str_starts_with($client->logo_url, 'http') ? $client->logo_url : Storage::url($client->logo_url))
                                        : null;
                                    // Stagger effect
                                    $marginTop = ($index % 3 === 1) ? 'md:mt-16' : (($index % 3 === 2) ? 'md:mt-32' : '');
                                @endphp
                                <div class="group {{ $marginTop }}">
                                    <div class="aspect-[4/3] flex items-center justify-center p-8 bg-neutral-50 mb-6 transition-colors duration-300 group-hover:bg-neutral-100">
                                        @if($client->website_url)
                                            <a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full flex items-center justify-center">
                                                @if($logo)
                                                    <img src="{{ $logo }}" alt="{{ $client->name }}" class="max-h-16 w-auto object-contain opacity-80 group-hover:opacity-100 transition-opacity" loading="lazy">
                                                @else
                                                    <span class="font-display text-xl text-gray-400 group-hover:text-black">{{ $client->name }}</span>
                                                @endif
                                            </a>
                                        @else
                                            @if($logo)
                                                <img src="{{ $logo }}" alt="{{ $client->name }}" class="max-h-16 w-auto object-contain opacity-80" loading="lazy">
                                            @else
                                                <span class="font-display text-xl text-gray-400">{{ $client->name }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <h4 class="font-display text-xl uppercase text-[#363439]">{{ $client->name }}</h4>
                                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-1">Client</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Older Projects --}}
                <div>
                    <div class="flex flex-col md:flex-row md:items-end gap-4 mb-12 border-b border-black/10 pb-6">
                        <h2 class="font-display text-3xl md:text-4xl uppercase text-[#363439]">
                            {{ $olderHeading }}
                        </h2>
                        @if($olderSubheading)
                            <p class="text-sm font-bold uppercase tracking-widest text-gray-400 md:mb-1 md:ml-4">
                                {{ $olderSubheading }}
                            </p>
                        @endif
                    </div>
                    
                    {{-- Simple list for older projects if we had them, or just a placeholder --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        {{-- Placeholder for Older Projects since we don't have a separate model yet --}}
                        <div class="p-6 border border-gray-100 bg-neutral-50">
                            <h4 class="font-display text-lg uppercase text-[#363439]">2019-2023 Archive</h4>
                            <p class="text-xs text-gray-500 mt-2">Various motion graphics and 3D projects.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Footer --}}
    <footer class="bg-white text-black px-6 pb-10">
        <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 text-sm text-gray-600 sm:flex-row sm:items-center">
            <p>© {{ date('Y') }} {{ $settings->site_title ?? 'Laurensius Dimas' }}.</p>

            @if($settings->social_links && count($settings->social_links) > 0)
                <div class="flex flex-wrap gap-4">
                    @foreach($settings->social_links as $link)
                        <a
                            href="{{ $link['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm uppercase tracking-[0.2em] text-gray-700 transition hover:text-black"
                        >
                            {{ $link['platform'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </footer>
</div>
