import './bootstrap';

/**
 * Reveal on Scroll - Intersection Observer Animation System
 * 
 * Usage:
 *   <div class="reveal-fade-up">Content</div>
 *   <div class="reveal-fade-up reveal-delay-200">Delayed content</div>
 * 
 * Available classes:
 *   - reveal-fade-up: Fade in from below (default, recommended)
 *   - reveal-fade: Simple opacity fade
 *   - reveal-fade-left: Slide in from left
 *   - reveal-fade-right: Slide in from right  
 *   - reveal-scale: Scale up from 90%
 *   - reveal-blur: Fade in with blur effect
 *   - reveal-clip: Clip/wipe reveal from bottom
 * 
 * Delay modifiers:
 *   - reveal-delay-100 through reveal-delay-500
 * 
 * Video handling:
 *   - Videos with data-lazy-video are lazy-loaded when in viewport
 *   - Videos fade in smoothly after they can play
 */
document.addEventListener('DOMContentLoaded', () => {
    // Select all elements with reveal classes
    const revealElements = document.querySelectorAll(
        '.reveal-fade-up, .reveal-fade, .reveal-fade-left, .reveal-fade-right, .reveal-scale, .reveal-blur, .reveal-clip'
    );

    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Immediately reveal all elements if user prefers reduced motion
        revealElements.forEach(el => el.classList.add('revealed'));
    } else {
        // Create Intersection Observer for reveal animations
        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        revealElements.forEach(el => revealObserver.observe(el));
    }

    /**
     * Lazy Video Loading
     * Videos with data-lazy-video attribute will:
     * 1. Start hidden/transparent
     * 2. Load when scrolled into view
     * 3. Fade in smoothly once they can play
     */
    const lazyVideos = document.querySelectorAll('video[data-lazy-video]');
    
    if (lazyVideos.length > 0) {
        const videoObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    const video = entry.target;
                    
                    if (entry.isIntersecting) {
                        // Video is in viewport - start loading and playing
                        if (video.dataset.src) {
                            video.src = video.dataset.src;
                            delete video.dataset.src;
                        }
                        
                        // Wait for video to be ready to play
                        const playWhenReady = () => {
                            video.play().then(() => {
                                // Video started playing - fade it in
                                video.classList.add('video-loaded');
                            }).catch(() => {
                                // Autoplay blocked - still show the video
                                video.classList.add('video-loaded');
                            });
                        };
                        
                        if (video.readyState >= 3) {
                            // Already can play
                            playWhenReady();
                        } else {
                            // Wait for canplay event
                            video.addEventListener('canplay', playWhenReady, { once: true });
                            video.load();
                        }
                        
                        videoObserver.unobserve(video);
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '100px 0px 100px 0px' // Start loading slightly before visible
            }
        );

        lazyVideos.forEach(video => videoObserver.observe(video));
    }
});
