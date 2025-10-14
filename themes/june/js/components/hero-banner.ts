/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 */

(() => {
    const heroBanners = document.querySelectorAll<HTMLElement>('.hero-banner');
    
    if (!heroBanners.length) return;

    const observerOptions: IntersectionObserverInit = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3, // Trigger when 30% of the hero banner is visible
    };

    const observer = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
        entries.forEach((entry: IntersectionObserverEntry) => {
            if (entry.isIntersecting) {
                // Add in-view class to trigger SCSS animations
                entry.target.classList.add('in-view');
            } else {
                // Remove in-view class to reset animations
                entry.target.classList.remove('in-view');
            }
        });
    }, observerOptions);

    // Observe all hero banners
    heroBanners.forEach((banner: HTMLElement) => {
        observer.observe(banner);
    });
})();

