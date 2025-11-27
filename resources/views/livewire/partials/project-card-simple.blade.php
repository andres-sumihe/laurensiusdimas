@props(['project'])

<div class="group relative aspect-[4/5] overflow-hidden bg-gray-100 cursor-pointer">
    @php
        $mediaItems = $project->media_items ?? [];
        $firstMedia = $mediaItems[0] ?? null;
        $imageUrl = null;
        $videoUrl = null;

        if ($firstMedia) {
            if ($firstMedia['type'] === 'image') {
                $imageUrl = str_starts_with($firstMedia['url'], 'http') 
                    ? $firstMedia['url'] 
                    : Storage::url($firstMedia['url']);
            } elseif ($firstMedia['type'] === 'video') {
                $videoUrl = str_starts_with($firstMedia['url'], 'http') 
                    ? $firstMedia['url'] 
                    : Storage::url($firstMedia['url']);
            }
        }
    @endphp

    {{-- Media --}}
    @if($imageUrl)
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $project->title }}" 
            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
            loading="lazy"
        >
    @elseif($videoUrl)
        <video 
            src="{{ $videoUrl }}" 
            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
            autoplay muted loop playsinline
        ></video>
    @else
        <div class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
            <span class="text-xs uppercase tracking-widest">No Media</span>
        </div>
    @endif

    {{-- Overlay Content --}}
    <div class="absolute inset-0 bg-black/0 transition-colors duration-300 group-hover:bg-black/20"></div>
    
    <div class="absolute bottom-0 left-0 w-full p-4 text-white opacity-0 transform translate-y-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0">
        <h3 class="font-display text-lg uppercase tracking-wide leading-tight mb-1">{{ $project->title }}</h3>
        <p class="font-body text-xs text-gray-200">{{ $project->client ?? 'Personal Project' }}</p>
    </div>
</div>
