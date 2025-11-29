@php
    use Illuminate\Support\Facades\Storage;

    $layoutRaw = $project->layout ?? 'three_two';
    // Back-compat with previous layout names
    $layout = match ($layoutRaw) {
        'collage_3_2' => 'three_two',
        'default' => 'single',
        'full' => 'single',
        'split' => 'two',
        default => $layoutRaw,
    };
    
    // Use the gridMedia relationship (unified project_media table with slot_index)
    // Falls back to old media_items JSON if gridMedia is empty (for backwards compatibility during migration)
    $mediaFromDb = $project->gridMedia ?? collect();
    
    if ($mediaFromDb->count() > 0) {
        // New unified table approach
        $mediaItems = $mediaFromDb->map(function ($media) {
            $url = $media->url ?? null;
            $thumb = $media->thumbnail_url ?? null;
            return [
                'type' => $media->type ?? 'image',
                'url' => $url ? (str_starts_with($url, 'http') ? $url : Storage::url($url)) : null,
                'thumb' => $thumb ? (str_starts_with($thumb, 'http') ? $thumb : Storage::url($thumb)) : null,
            ];
        })->filter(fn ($m) => $m['url'])->values();
    } else {
        // Fallback to old JSON column during migration period
        $mediaItems = collect($project->media_items ?? [])->map(function ($item) {
            $url = $item['url'] ?? null;
            $thumb = $item['thumbnailUrl'] ?? null;
            return [
                'type' => $item['type'] ?? 'image',
                'url' => $url ? (str_starts_with($url, 'http') ? $url : Storage::url($url)) : null,
                'thumb' => $thumb ? (str_starts_with($thumb, 'http') ? $thumb : Storage::url($thumb)) : null,
            ];
        })->filter(fn ($m) => $m['url'])->values();
    }
@endphp

{{-- 
    Safelist for Tailwind JIT (static references so classes get compiled):
    col-span-3 col-span-4 col-span-6 col-span-12 grid-cols-12
--}}
<article class="space-y-2 sm:space-y-3">
    @if($layout === 'single')
        {{-- Single Hero Layout --}}
        @php $item = $mediaItems->first(); @endphp
        @if($item)
            <div class="w-full overflow-hidden bg-neutral-100 cursor-pointer" 
                 @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                <div class="aspect-video">
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

    @elseif($layout === 'two')
        {{-- Two (Split) Layout: 2 items side by side - Stack on mobile --}}
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-0 w-full">
            @foreach($mediaItems->take(2) as $item)
                <div class="sm:col-span-6 relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
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

    @elseif($layout === 'three_two')
        {{-- Three-Two (5-Up) Layout: Row 1 = 3 items, Row 2 = 2 items - Adjust for mobile --}}
        <div class="grid grid-cols-2 sm:grid-cols-12 gap-1 sm:gap-0 w-full">
            @foreach($mediaItems->take(3) as $index => $item)
                <div class="{{ $index === 0 ? 'col-span-2 sm:col-span-4' : 'col-span-1 sm:col-span-4' }} relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                    @if($item['type'] === 'video')
                        <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                            <source src="{{ $item['url'] }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
            @endforeach
            @foreach($mediaItems->slice(3)->take(2) as $item)
                <div class="col-span-1 sm:col-span-6 relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
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

    @elseif($layout === 'three_three')
        {{-- Three-Three (6-Up) Layout: Row 1 = 3 items, Row 2 = 3 items --}}
        <div class="grid grid-cols-2 sm:grid-cols-12 gap-1 sm:gap-0 w-full">
            @foreach($mediaItems->take(6) as $index => $item)
                <div class="{{ $index % 3 === 0 && $index < 6 ? 'col-span-2 sm:col-span-4' : 'col-span-1 sm:col-span-4' }} relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
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

    @elseif($layout === 'four_one')
        {{-- Four-One (5-Up) Layout: Row 1 = 4 items, Row 2 = 1 full-width item --}}
        <div class="grid grid-cols-2 sm:grid-cols-12 gap-1 sm:gap-0 w-full">
            @foreach($mediaItems->take(4) as $item)
                <div class="col-span-1 sm:col-span-3 relative group overflow-hidden bg-neutral-100 aspect-square sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                    @if($item['type'] === 'video')
                        <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                            <source src="{{ $item['url'] }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
            @endforeach
            @if($mediaItems->count() > 4)
                @php $item = $mediaItems->get(4); @endphp
                <div class="col-span-2 sm:col-span-12 relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-[21/9] cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                    @if($item['type'] === 'video')
                        <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                            <source src="{{ $item['url'] }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
            @endif
        </div>

    @elseif($layout === 'four_two')
        {{-- Four-Two (6-Up) Layout: Row 1 = 4 items, Row 2 = 2 items --}}
        <div class="grid grid-cols-2 sm:grid-cols-12 gap-1 sm:gap-0 w-full">
            @foreach($mediaItems->take(4) as $item)
                <div class="col-span-1 sm:col-span-3 relative group overflow-hidden bg-neutral-100 aspect-square sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                    @if($item['type'] === 'video')
                        <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
                            <source src="{{ $item['url'] }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $item['url'] }}" alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
            @endforeach
            @foreach($mediaItems->slice(4)->take(2) as $item)
                <div class="col-span-1 sm:col-span-6 relative group overflow-hidden bg-neutral-100 aspect-video sm:aspect-4/3 md:aspect-video cursor-pointer"
                     @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
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

    @else
        {{-- Fallback: treat as single --}}
        @php $item = $mediaItems->first(); @endphp
        @if($item)
            <div class="w-full overflow-hidden bg-neutral-100 cursor-pointer"
                 @click="$dispatch('open-lightbox', { url: '{{ $item['url'] }}', type: '{{ $item['type'] }}' })">
                <div class="aspect-video">
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
    @endif

    {{-- Project Title & Subtitle --}}
    <div class="space-y-0.5 sm:space-y-1">
        <h3 class="font-display text-sm sm:text-xl md:text-[24px] uppercase text-[#363439]">
            {{ strtoupper($project->title) }}
        </h3>
        @if($project->work_type || $project->work_description)
            <p class="text-[10px] sm:text-sm uppercase tracking-[0.15em] sm:tracking-wider text-gray-600">
                @if($project->work_type)
                    <span class="font-semibold">{{ ucfirst(str_replace('-', ' ', $project->work_type)) }}</span>
                @endif
                @if($project->work_type && $project->work_description)
                    <span class="mx-1">|</span>
                @endif
                @if($project->work_description)
                    {{ $project->work_description }}
                @endif
            </p>
        @elseif($project->subtitle)
            {{-- Fallback for old data --}}
            <p class="text-[10px] sm:text-sm uppercase tracking-[0.15em] sm:tracking-wider text-gray-600">
                {{ $project->subtitle }}
            </p>
        @endif
    </div>
</article>
