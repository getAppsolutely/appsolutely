/**
 * Video Showcase Component
 * Handles video autoplay, mobile compatibility, and performance optimization
 */

class VideoShowcase {
    private video: HTMLVideoElement | null;
    private observer: IntersectionObserver | null;

    constructor() {
        this.video = null;
        this.observer = null;
    }

    init(): void {
        this.video = document.querySelector<HTMLVideoElement>('.video-showcase video');
        if (this.video) {
            this.setupVideo();
            this.setupIntersectionObserver();
            this.handleVideoEvents();
        }
    }

    setupVideo(): void {
        if (!this.video) return;

        // Ensure video plays on mobile devices
        this.video.setAttribute('playsinline', '');
        this.video.setAttribute('webkit-playsinline', '');

        // Set additional mobile-friendly attributes
        this.video.setAttribute('preload', 'metadata');
    }

    handleVideoEvents(): void {
        if (!this.video) return;

        // Try to play video when it's loaded
        this.video.addEventListener('loadeddata', () => {
            if (this.video && this.video.readyState >= 3) {
                this.playVideo();
            }
        });

        // Handle video ready state changes
        this.video.addEventListener('canplaythrough', () => {
            this.playVideo();
        });

        // Handle video errors
        this.video.addEventListener('error', () => {
            this.handleVideoError();
        });
    }

    playVideo(): void {
        if (!this.video) return;

        // Handle play promise for modern browsers
        const playPromise = this.video.play();

        if (playPromise !== undefined) {
            playPromise.catch(() => {
                this.handleAutoplayBlocked();
            });
        }
    }

    handleAutoplayBlocked(): void {
        // Add click listener to start video on user interaction (browsers require user gesture for autoplay)
        document.addEventListener(
            'click',
            () => {
                this.video?.play().catch(() => {});
            },
            { once: true }
        );
    }

    handleVideoError(): void {
        // Hide video and show fallback image if available
        const fallbackImage = document.querySelector<HTMLImageElement>(
            '.video-showcase .video-showcase__mobile-fallback img'
        );
        if (fallbackImage && this.video) {
            fallbackImage.style.display = 'block';
            this.video.style.display = 'none';
        }
    }

    setupIntersectionObserver(): void {
        if (!this.video) return;

        // Pause video when not in view (performance optimization)
        this.observer = new IntersectionObserver(
            (entries: IntersectionObserverEntry[]) => {
                entries.forEach((entry: IntersectionObserverEntry) => {
                    if (entry.isIntersecting) {
                        // Video is in view - play it
                        this.video?.play().catch(() => {});
                    } else {
                        // Video is out of view - pause it to save resources
                        this.video?.pause();
                    }
                });
            },
            {
                threshold: 0.5,
                rootMargin: '50px',
            }
        );

        this.observer.observe(this.video);
    }

    destroy(): void {
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

let videoShowcaseInstance: VideoShowcase | null = null;

function initVideoShowcase(): void {
    videoShowcaseInstance?.destroy();
    videoShowcaseInstance = new VideoShowcase();
    videoShowcaseInstance.init();
}

export { initVideoShowcase as init };
export default VideoShowcase;
