@props(['project'])

<div class="group relative w-full h-full overflow-hidden bg-gray-100">
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
            class="h-full w-full object-cover"
            loading="lazy"
        >
    @elseif($videoUrl)
        <video 
            src="{{ $videoUrl }}" 
            class="h-full w-full object-cover"
            autoplay muted loop playsinline
        ></video>
    @else
        <div class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
            <span class="text-xs uppercase tracking-widest">No Media</span>
        </div>
    @endif
</div>
