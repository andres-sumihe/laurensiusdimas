import './bootstrap';
import { animate, stagger, createTimeline, utils } from 'animejs';

// Export anime.js functions for global use in Alpine.js components
window.anime = { animate, stagger, createTimeline, utils };

/**
 * ========================================
 * ANIME.JS ANIMATION SYSTEM
 * ========================================
 * All animations are powered by anime.js for consistent, 
 * smooth, and performant animations across the site.
 */

// Animation configuration presets
const ANIMATION_CONFIG = {
    // Easing functions (anime.js v4 built-in easings)
    ease: {
        smooth: 'outExpo',      // Smooth exponential deceleration
        bounce: 'outElastic(1, 0.5)',
        snappy: 'outQuart',
        gentle: 'inOutQuad',
        standard: 'outQuad',    // Standard easing for most animations
    },
    // Duration presets (ms)
    duration: {
        fast: 400,
        normal: 800,
        slow: 1200,
    },
    // Transform values
    transform: {
        fadeUp: { y: 40, opacity: 0 },
        fadeLeft: { x: -40, opacity: 0 },
        fadeRight: { x: 40, opacity: 0 },
        scale: { scale: 0.9, opacity: 0 },
        blur: { filter: 'blur(10px)', opacity: 0 },
    }
};

/**
 * Get delay from element's class (reveal-delay-100, etc.)
 */
function getDelayFromClass(element) {
    const classes = element.className.split(' ');
    for (const cls of classes) {
        const match = cls.match(/reveal-delay-(\d+)/);
        if (match) return parseInt(match[1]);
    }
    return 0;
}

/**
 * Get animation type from element's class
 */
function getAnimationType(element) {
    if (element.classList.contains('reveal-fade-up')) return 'fadeUp';
    if (element.classList.contains('reveal-fade-left')) return 'fadeLeft';
    if (element.classList.contains('reveal-fade-right')) return 'fadeRight';
    if (element.classList.contains('reveal-scale')) return 'scale';
    if (element.classList.contains('reveal-blur')) return 'blur';
    if (element.classList.contains('reveal-clip')) return 'clip';
    if (element.classList.contains('reveal-fade')) return 'fade';
    return 'fadeUp'; // default
}

/**
 * Animate element reveal with anime.js
 */
function animateReveal(element) {
    const type = getAnimationType(element);
    const delay = getDelayFromClass(element);
    
    // Set initial state
    if (type === 'fadeUp') {
        utils.set(element, { translateY: 40, opacity: 0 });
        animate(element, {
            translateY: 0,
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else if (type === 'fadeLeft') {
        utils.set(element, { translateX: -40, opacity: 0 });
        animate(element, {
            translateX: 0,
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else if (type === 'fadeRight') {
        utils.set(element, { translateX: 40, opacity: 0 });
        animate(element, {
            translateX: 0,
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else if (type === 'scale') {
        utils.set(element, { scale: 0.9, opacity: 0 });
        animate(element, {
            scale: 1,
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else if (type === 'blur') {
        utils.set(element, { filter: 'blur(10px)', opacity: 0 });
        animate(element, {
            filter: 'blur(0px)',
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else if (type === 'clip') {
        utils.set(element, { clipPath: 'inset(100% 0 0 0)' });
        animate(element, {
            clipPath: 'inset(0% 0 0 0)',
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    } else {
        // Simple fade
        utils.set(element, { opacity: 0 });
        animate(element, {
            opacity: 1,
            duration: ANIMATION_CONFIG.duration.normal,
            ease: ANIMATION_CONFIG.ease.smooth,
            delay: delay,
        });
    }
    
    element.classList.add('revealed');
}

/**
 * Initialize reveal elements with proper initial states
 */
function initRevealElements() {
    const revealElements = document.querySelectorAll(
        '.reveal-fade-up, .reveal-fade, .reveal-fade-left, .reveal-fade-right, .reveal-scale, .reveal-blur, .reveal-clip'
    );
    
    revealElements.forEach(element => {
        const type = getAnimationType(element);
        
        // Set initial hidden state based on animation type
        if (type === 'fadeUp') {
            utils.set(element, { translateY: 40, opacity: 0 });
        } else if (type === 'fadeLeft') {
            utils.set(element, { translateX: -40, opacity: 0 });
        } else if (type === 'fadeRight') {
            utils.set(element, { translateX: 40, opacity: 0 });
        } else if (type === 'scale') {
            utils.set(element, { scale: 0.9, opacity: 0 });
        } else if (type === 'blur') {
            utils.set(element, { filter: 'blur(10px)', opacity: 0 });
        } else if (type === 'clip') {
            utils.set(element, { clipPath: 'inset(100% 0 0 0)' });
        } else {
            utils.set(element, { opacity: 0 });
        }
    });
    
    return revealElements;
}

/**
 * Animate video fade-in with anime.js
 */
function animateVideoFadeIn(video) {
    animate(video, {
        opacity: [0, 1],
        duration: 500,
        ease: 'outQuad',
    });
    video.classList.add('video-loaded');
}

/**
 * ========================================
 * HERO ANIMATIONS (exposed for Alpine.js)
 * ========================================
 */

/**
 * Animate hero logo from center to corner
 */
window.animateLogoToCorner = function(logoElement, cornerPosition) {
    return animate(logoElement, {
        top: cornerPosition.top,
        left: cornerPosition.left,
        translateX: 0,
        translateY: 0,
        width: cornerPosition.width,
        duration: 900,
        ease: 'outQuart',
    });
};

/**
 * Animate hero loader fade out
 */
window.animateLoaderFadeOut = function(loaderElement) {
    return animate(loaderElement, {
        opacity: 0,
        duration: 800,
        ease: 'outQuad',
        complete: () => {
            loaderElement.style.visibility = 'hidden';
            loaderElement.style.pointerEvents = 'none';
        }
    });
};

/**
 * Create morphing blob animation
 */
window.createMorphingBlobAnimation = function(blobElement) {
    // Morphing border-radius animation
    const morphAnimation = createTimeline({
        loop: true,
        defaults: {
            duration: 8000,
            ease: 'inOutSine',
        }
    });
    
    morphAnimation
        .add(blobElement, {
            borderRadius: [
                '60% 40% 70% 30% / 40% 50% 60% 50%',
                '40% 60% 30% 70% / 60% 30% 70% 40%',
            ],
            rotate: [0, 90],
            scale: [1, 1.05],
        })
        .add(blobElement, {
            borderRadius: '50% 50% 40% 60% / 50% 60% 40% 50%',
            rotate: 180,
            scale: 1,
        })
        .add(blobElement, {
            borderRadius: '70% 30% 60% 40% / 30% 70% 40% 60%',
            rotate: 270,
            scale: 1.05,
        })
        .add(blobElement, {
            borderRadius: '60% 40% 70% 30% / 40% 50% 60% 50%',
            rotate: 360,
            scale: 1,
        });
    
    return morphAnimation;
};

/**
 * Create logo breathing animation
 */
window.createLogoBreathAnimation = function(logoElement) {
    return animate(logoElement, {
        scale: [1, 1.05, 1],
        filter: [
            'drop-shadow(0 0 20px rgba(255, 255, 255, 0.5))',
            'drop-shadow(0 0 30px rgba(255, 255, 255, 0.7))',
            'drop-shadow(0 0 20px rgba(255, 255, 255, 0.5))',
        ],
        duration: 2000,
        ease: 'inOutSine',
        loop: true,
    });
};

/**
 * ========================================
 * MAIN INITIALIZATION
 * ========================================
 */
document.addEventListener('DOMContentLoaded', () => {
    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Show all elements immediately without animation
        const revealElements = document.querySelectorAll(
            '.reveal-fade-up, .reveal-fade, .reveal-fade-left, .reveal-fade-right, .reveal-scale, .reveal-blur, .reveal-clip'
        );
        revealElements.forEach(el => {
            utils.set(el, { opacity: 1, translateX: 0, translateY: 0, scale: 1, filter: 'none', clipPath: 'none' });
            el.classList.add('revealed');
        });
    } else {
        // Initialize reveal elements with hidden states
        const revealElements = initRevealElements();
        
        // Create Intersection Observer for reveal animations
        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('revealed')) {
                        animateReveal(entry.target);
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
     * Lazy Video Loading with anime.js fade-in
     */
    const lazyVideos = document.querySelectorAll('video[data-lazy-video]');
    
    if (lazyVideos.length > 0) {
        // Set initial state for all lazy videos
        lazyVideos.forEach(video => {
            utils.set(video, { opacity: 0 });
        });
        
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
                                animateVideoFadeIn(video);
                            }).catch(() => {
                                // Autoplay blocked - still show the video
                                animateVideoFadeIn(video);
                            });
                        };
                        
                        if (video.readyState >= 3) {
                            playWhenReady();
                        } else {
                            video.addEventListener('canplay', playWhenReady, { once: true });
                            video.load();
                        }
                        
                        videoObserver.unobserve(video);
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '100px 0px 100px 0px'
            }
        );

        lazyVideos.forEach(video => videoObserver.observe(video));
    }
});
