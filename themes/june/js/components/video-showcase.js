/**
 * Video Showcase Component
 * Handles video autoplay, mobile compatibility, and performance optimization
 */

class VideoShowcase {
    constructor() {
        this.video = null;
        this.observer = null;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.video = document.querySelector('.video-showcase video');
            if (this.video) {
                this.setupVideo();
                this.setupIntersectionObserver();
                this.handleVideoEvents();
            }
        });
    }

    setupVideo() {
        // Ensure video plays on mobile devices
        this.video.setAttribute('playsinline', '');
        this.video.setAttribute('webkit-playsinline', '');
        
        // Set additional mobile-friendly attributes
        this.video.setAttribute('preload', 'metadata');
    }

    handleVideoEvents() {
        // Add loading state
        this.video.addEventListener('loadstart', () => {
            console.log('Video loading started');
        });

        // Try to play video when it's loaded
        this.video.addEventListener('loadeddata', () => {
            if (this.video.readyState >= 3) {
                this.playVideo();
            }
        });

        // Handle video ready state changes
        this.video.addEventListener('canplaythrough', () => {
            this.playVideo();
        });

        // Handle video errors
        this.video.addEventListener('error', (e) => {
            console.error('Video error:', e);
            this.handleVideoError();
        });

        // Handle video ended (for non-looping videos)
        this.video.addEventListener('ended', () => {
            console.log('Video ended');
        });
    }

    playVideo() {
        // Handle play promise for modern browsers
        const playPromise = this.video.play();
        
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    console.log('Video autoplay started successfully');
                })
                .catch((error) => {
                    console.log('Video autoplay prevented by browser policy:', error);
                    this.handleAutoplayBlocked();
                });
        }
    }

    handleAutoplayBlocked() {
        // Could add user interaction to start video
        // For example, show a play button overlay
        console.log('Autoplay blocked - could show play button');
        
        // Option: Add click listener to start video on user interaction
        document.addEventListener('click', () => {
            this.video.play().catch(e => console.log('Manual play failed:', e));
        }, { once: true });
    }

    handleVideoError() {
        // Hide video and show fallback image if available
        const fallbackImage = document.querySelector('.video-showcase .mobile-fallback img');
        if (fallbackImage) {
            fallbackImage.style.display = 'block';
            this.video.style.display = 'none';
        }
    }

    setupIntersectionObserver() {
        // Pause video when not in view (performance optimization)
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Video is in view - play it
                    this.video.play().catch(e => console.log('Play failed:', e));
                } else {
                    // Video is out of view - pause it to save resources
                    this.video.pause();
                }
            });
        }, { 
            threshold: 0.5,
            rootMargin: '50px'
        });

        this.observer.observe(this.video);
    }

    destroy() {
        // Clean up observers and event listeners
        if (this.observer) {
            this.observer.disconnect();
        }
        
        if (this.video) {
            this.video.pause();
            this.video.removeAttribute('src');
            this.video.load();
        }
    }
}

// Initialize the video showcase
const videoShowcase = new VideoShowcase();

// Export for potential use in other modules
export default VideoShowcase; 