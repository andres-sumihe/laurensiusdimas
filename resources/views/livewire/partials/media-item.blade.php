{{-- 
    Reusable media item component for project cards
    Supports: image, video (mp4/webm), and YouTube embeds with inline autoplay
    
    Required variables:
    - $item: array with keys: type, url, thumb, youtubeId (optional)
    - $alt: string for alt text
    
    Optional variables:
    - $uniqueId: string for YouTube player (auto-generated if not provided)
    - $showControls: bool - show mute/play buttons for YouTube (default: true)
--}}
@php
    $uniqueId = $uniqueId ?? 'yt-media-' . uniqid();
    $showControls = $showControls ?? true;
@endphp

@if($item['type'] === 'youtube' && !empty($item['youtubeId']))
    {{-- YouTube with inline autoplay on scroll --}}
    @include('livewire.partials.youtube-inline-player', [
        'youtubeId' => $item['youtubeId'],
        'thumbnailUrl' => $item['thumb'],
        'uniqueId' => $uniqueId,
        'showControls' => $showControls,
    ])
@elseif($item['type'] === 'video')
    <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
        <source src="{{ $item['url'] }}" type="video/mp4">
    </video>
@else
    <img src="{{ $item['url'] }}" alt="{{ $alt }}" class="h-full w-full object-cover" loading="lazy">
@endif
