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
 *   - reveal-hero: Faster animation for hero section
 * 
 * Delay modifiers:
 *   - reveal-delay-100 through reveal-delay-500
 */
document.addEventListener('DOMContentLoaded', () => {
    // Select all elements with reveal classes
    const revealElements = document.querySelectorAll(
        '.reveal-fade-up, .reveal-fade, .reveal-fade-left, .reveal-fade-right, .reveal-scale, .reveal-blur, .reveal-clip, .reveal-hero'
    );

    if (revealElements.length === 0) return;

    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Immediately reveal all elements if user prefers reduced motion
        revealElements.forEach(el => el.classList.add('revealed'));
        return;
    }

    // Create Intersection Observer
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add revealed class to trigger animation
                    entry.target.classList.add('revealed');
                    // Stop observing once revealed (one-time animation)
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            // Trigger when 15% of element is visible
            threshold: 0.15,
            // Start animation slightly before element enters viewport
            rootMargin: '0px 0px -50px 0px'
        }
    );

    // Observe all reveal elements
    revealElements.forEach(el => observer.observe(el));

    // Special handling for hero section - reveal immediately on load
    const heroElements = document.querySelectorAll('.reveal-hero');
    heroElements.forEach((el, index) => {
        // Stagger hero animations
        setTimeout(() => {
            el.classList.add('revealed');
            observer.unobserve(el);
        }, 100 + (index * 150));
    });
});
