/**
 * Media Slider Carousel Component
 * Uses Swiper.js for carousel functionality
 */

import Swiper from 'swiper/bundle';

class MediaSliderCarousel {
    constructor() {
        this.swipers = new Map();
        this.init();
    }

    init() {
        // Initialize existing sliders on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeSliders();
        });

        // Handle Livewire re-renders
        document.addEventListener('livewire:navigated', () => {
            this.initializeSliders();
        });

        // Handle component updates
        document.addEventListener('livewire:updated', () => {
            this.initializeSliders();
        });
    }

    initializeSliders() {
        // Find all media slider carousels
        const sliders = document.querySelectorAll('.swiper[data-slider-id]');
        
        sliders.forEach(sliderElement => {
            const sliderId = sliderElement.dataset.sliderId;
            
            // Skip if already initialized
            if (this.swipers.has(sliderId)) {
                return;
            }

            // Store reference to this instance for callbacks
            const self = this;

            // Initialize Swiper
            const swiper = new Swiper('.' + sliderId, {
                slidesPerView: "auto",
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },

                navigation: {
                    nextEl: '.swiper-button-next[data-slider-id="' + sliderId + '"]',
                    prevEl: '.swiper-button-prev[data-slider-id="' + sliderId + '"]',
                },
                keyboard: {
                    enabled: true,
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    768: {
                        slidesPerView: 'auto',
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 'auto',
                        spaceBetween: 30
                    }
                },
                on: {
                    init: function() {
                        console.log('Media slider initialized:', sliderId);
                    },
                    slideChange: function() {
                        // Handle slide change events if needed
                        self.pauseAllVideos();
                    }
                }
            });

            // Store swiper instance
            this.swipers.set(sliderId, swiper);

            // Add video handling
            this.handleVideoSlides(sliderElement, swiper);
        });
    }

    handleVideoSlides(sliderElement, swiper) {
        const videoSlides = sliderElement.querySelectorAll('.swiper-slide video');
        const self = this;
        
        videoSlides.forEach(video => {
            // Pause video when slide changes
            swiper.on('slideChange', function() {
                video.pause();
            });

            // Auto-play video when slide becomes active
            swiper.on('slideChangeTransitionEnd', function() {
                const activeSlide = sliderElement.querySelector('.swiper-slide-active');
                const activeVideo = activeSlide?.querySelector('video');
                
                if (activeVideo && activeVideo === video) {
                    // Optional: auto-play video in active slide
                    // activeVideo.play();
                }
            });
        });
    }

    pauseAllVideos() {
        document.querySelectorAll('.swiper-slide video').forEach(video => {
            video.pause();
        });
    }

    // Destroy specific slider
    destroySlider(sliderId) {
        if (this.swipers.has(sliderId)) {
            this.swipers.get(sliderId).destroy(true, true);
            this.swipers.delete(sliderId);
        }
    }

    // Destroy all sliders
    destroyAll() {
        this.swipers.forEach((swiper, sliderId) => {
            swiper.destroy(true, true);
        });
        this.swipers.clear();
    }
}

// Initialize the media slider carousel
const mediaSliderCarousel = new MediaSliderCarousel();

// Export for potential external use
window.MediaSliderCarousel = MediaSliderCarousel;