{{-- 
    Reusable media item component for project cards
    Supports: image, video (mp4/webm), and YouTube embeds
    
    Required variables:
    - $item: array with keys: type, url, thumb, youtubeId (optional)
    - $alt: string for alt text
--}}
@if($item['type'] === 'youtube')
    {{-- YouTube thumbnail with play button overlay --}}
    <img src="{{ $item['thumb'] }}" alt="{{ $alt }}" class="h-full w-full object-cover" loading="lazy">
    <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors">
        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </div>
    </div>
@elseif($item['type'] === 'video')
    <video class="h-full w-full object-cover" autoplay muted loop playsinline poster="{{ $item['thumb'] }}">
        <source src="{{ $item['url'] }}" type="video/mp4">
    </video>
@else
    <img src="{{ $item['url'] }}" alt="{{ $alt }}" class="h-full w-full object-cover" loading="lazy">
@endif
