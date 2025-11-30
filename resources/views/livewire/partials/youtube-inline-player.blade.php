{{-- 
    Reusable YouTube Inline Player Component
    Autoplays muted when scrolled into view, loops continuously
    
    Required variables:
    - $youtubeId: string - The YouTube video ID
    - $thumbnailUrl: string - Thumbnail URL (fallback: auto-generated from YouTube)
    - $uniqueId: string - Unique identifier for this player instance
    
    Optional variables:
    - $showControls: bool - Whether to show mute/play buttons (default: true)
    - $aspectRatio: string - CSS aspect ratio class (default: 'aspect-video')
--}}
@php
    $uniqueId = $uniqueId ?? 'yt-' . uniqid();
    $thumbnailUrl = $thumbnailUrl ?? 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
    $showControls = $showControls ?? true;
    $aspectRatio = $aspectRatio ?? 'aspect-video';
@endphp

<div 
    x-data="{
        inView: false,
        player: null,
        isMuted: true,
        isPaused: false,
        playerId: '{{ $uniqueId }}',
        youtubeId: '{{ $youtubeId }}',
        thumbnailUrl: '{{ $thumbnailUrl }}',
        
        init() {
            // Setup Intersection Observer for autoplay on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                        if (!this.inView) {
                            this.inView = true;
                            this.$nextTick(() => this.loadPlayer());
                        }
                    } else if (!entry.isIntersecting || entry.intersectionRatio < 0.3) {
                        if (this.inView && this.player) {
                            this.player.pauseVideo();
                            this.isPaused = true;
                        }
                        this.inView = false;
                    }
                });
            }, { threshold: [0, 0.3, 0.5, 1] });
            
            observer.observe(this.$el);
        },
        
        loadPlayer() {
            if (this.player) {
                this.player.playVideo();
                this.isPaused = false;
                return;
            }
            
            // Wait for YouTube API
            if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                setTimeout(() => this.loadPlayer(), 100);
                return;
            }
            
            this.player = new YT.Player(this.playerId, {
                videoId: this.youtubeId,
                playerVars: {
                    autoplay: 1,
                    mute: 1,
                    loop: 1,
                    playlist: this.youtubeId,
                    controls: 0,
                    rel: 0,
                    modestbranding: 1,
                    fs: 0,
                    iv_load_policy: 3,
                    disablekb: 1,
                    playsinline: 1,
                    showinfo: 0,
                    origin: window.location.origin
                },
                events: {
                    onReady: (event) => {
                        event.target.playVideo();
                        this.isPaused = false;
                    },
                    onStateChange: (event) => {
                        // YT.PlayerState.ENDED = 0
                        if (event.data === 0) {
                            event.target.seekTo(0);
                            event.target.playVideo();
                        }
                        // Update pause state
                        this.isPaused = (event.data === 2); // YT.PlayerState.PAUSED = 2
                    }
                }
            });
        },
        
        toggleMute() {
            if (!this.player) return;
            if (this.isMuted) {
                this.player.unMute();
                this.player.setVolume(100);
            } else {
                this.player.mute();
            }
            this.isMuted = !this.isMuted;
        },
        
        togglePlay() {
            if (!this.player) return;
            if (this.isPaused) {
                this.player.playVideo();
            } else {
                this.player.pauseVideo();
            }
        }
    }"
    class="relative w-full h-full overflow-hidden"
>
    {{-- Thumbnail (shows before player loads) --}}
    <img 
        x-show="!inView"
        :src="thumbnailUrl"
        alt="Video thumbnail"
        class="absolute inset-0 w-full h-full object-cover"
        loading="lazy"
    />
    
    {{-- YouTube Player Container --}}
    <div 
        x-show="inView"
        :id="playerId"
        class="absolute inset-0 w-full h-full"
    ></div>
    
    @if($showControls)
    {{-- Control Buttons --}}
    <div class="absolute bottom-2 right-2 sm:bottom-3 sm:right-3 flex items-center gap-1.5 sm:gap-2 z-20">
        {{-- Play/Pause Button --}}
        <button 
            x-show="inView"
            @click.stop="togglePlay()"
            class="bg-black/70 hover:bg-black/90 text-white rounded-full p-1.5 sm:p-2 transition-all transform hover:scale-110 backdrop-blur-sm"
            :title="isPaused ? 'Play' : 'Pause'"
        >
            {{-- Play Icon --}}
            <svg x-show="isPaused" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
            {{-- Pause Icon --}}
            <svg x-show="!isPaused" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
            </svg>
        </button>
        
        {{-- Mute/Unmute Button --}}
        <button 
            x-show="inView"
            @click.stop="toggleMute()"
            class="bg-black/70 hover:bg-black/90 text-white rounded-full p-1.5 sm:p-2 transition-all transform hover:scale-110 backdrop-blur-sm"
            :title="isMuted ? 'Unmute' : 'Mute'"
        >
            {{-- Muted Icon --}}
            <svg x-show="isMuted" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
            </svg>
            {{-- Unmuted Icon --}}
            <svg x-show="!isMuted" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
            </svg>
        </button>
    </div>
    @endif
    
    {{-- Gradient overlay at bottom for button visibility --}}
    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
</div>
