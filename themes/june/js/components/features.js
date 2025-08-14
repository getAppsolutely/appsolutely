/**
 * Features Component JavaScript
 * Handles scroll-triggered animations for features titles and descriptions
 */

(() => {
    const featuresSections = document.querySelectorAll('.features, .features-fullscreen');
    
    if (!featuresSections.length) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2 // Trigger when 20% of the features section is visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add in-view class to trigger SCSS animations
                entry.target.classList.add('in-view');
                
                // Trigger staggered animations for title and descriptions
                const title = entry.target.querySelector('.features__title, .features-fullscreen__title');
                const descriptions = entry.target.querySelectorAll('.features__description, .features-fullscreen__description');
                
                if (title) {
                    title.classList.add('animate');
                }
                
                // Stagger the description animations
                descriptions.forEach((desc, index) => {
                    setTimeout(() => {
                        desc.classList.add('animate');
                    }, 200 + (index * 150)); // 200ms delay + 150ms stagger
                });
            } else {
                // Remove in-view class to reset animations
                entry.target.classList.remove('in-view');
                
                // Reset animation classes
                const title = entry.target.querySelector('.features__title, .features-fullscreen__title');
                const descriptions = entry.target.querySelectorAll('.features__description, .features-fullscreen__description');
                
                if (title) {
                    title.classList.remove('animate');
                }
                
                descriptions.forEach(desc => {
                    desc.classList.remove('animate');
                });
            }
        });
    }, observerOptions);

    // Observe all features sections
    featuresSections.forEach(section => {
        observer.observe(section);
    });
})();
