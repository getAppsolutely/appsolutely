/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 */

(() => {
    const heroBanners = document.querySelectorAll('.hero-banner');
    
    if (!heroBanners.length) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3 // Trigger when 30% of the hero banner is visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
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
    heroBanners.forEach(banner => {
        observer.observe(banner);
    });
})(); 