/**
 * Media Slider Carousel Component
 * Uses Swiper.js for carousel functionality
 */

import Swiper from 'swiper/bundle';
import type { Swiper as SwiperType, SwiperOptions } from 'swiper/types';

class MediaSliderCarousel {
    private swipers: Map<string, SwiperType>;

    constructor() {
        this.swipers = new Map();
        this.init();
    }

    init(): void {
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

    initializeSliders(): void {
        // Find all media slider carousels
        const sliders = document.querySelectorAll<HTMLElement>('.swiper[data-slider-id]');

        sliders.forEach((sliderElement: HTMLElement) => {
            const sliderId = sliderElement.dataset.sliderId;

            if (!sliderId) return;

            // Skip if already initialized
            if (this.swipers.has(sliderId)) {
                return;
            }

            // Store reference to this instance for callbacks
            const self = this;

            // Detect slider type
            const isSimpleSlider = sliderElement.closest('.media-slider-simple-container') !== null;

            // Base Swiper configuration
            const swiperConfig: SwiperOptions = {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                keyboard: {
                    enabled: true,
                },
                on: {
                    init: function (this: SwiperType) {
                        //console.log('Media slider initialized:', sliderId);
                        // Only update title slider if it exists
                        if (self.hasTitleSlider(sliderId)) {
                            self.updateTitleSlider(sliderId, 0);
                        }
                    },
                    slideChange: function (this: SwiperType) {
                        // Handle slide change events if needed
                        self.pauseAllVideos();
                        // Only update title slider if it exists
                        if (self.hasTitleSlider(sliderId)) {
                            self.updateTitleSlider(sliderId, this.realIndex);
                        }
                    },
                },
            };

            // Add type-specific configuration
            if (isSimpleSlider) {
                // Simple slider configuration
                Object.assign(swiperConfig, {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    navigation: {
                        nextEl: '.swiper-button-next[data-slider-id="' + sliderId + '"]',
                        prevEl: '.swiper-button-prev[data-slider-id="' + sliderId + '"]',
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                });
            } else {
                // Carousel slider configuration
                Object.assign(swiperConfig, {
                    slidesPerView: 'auto',
                    spaceBetween: 30,
                    navigation: {
                        nextEl: '.swiper-button-next[data-slider-id="' + sliderId + '"]',
                        prevEl: '.swiper-button-prev[data-slider-id="' + sliderId + '"]',
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 0,
                        },
                        768: {
                            slidesPerView: 'auto',
                            spaceBetween: 20,
                        },
                        1024: {
                            slidesPerView: 'auto',
                            spaceBetween: 30,
                        },
                    },
                });
            }

            // Initialize Swiper
            const swiper = new Swiper('.' + sliderId, swiperConfig);

            // Store swiper instance
            this.swipers.set(sliderId, swiper);

            // Add video handling
            this.handleVideoSlides(sliderElement, swiper);
        });
    }

    hasTitleSlider(sliderId: string): boolean {
        try {
            const sliderElement = document.querySelector(`[data-slider-id="${sliderId}"]`);
            if (!sliderElement) return false;

            const container = sliderElement.closest('.media-slider-carousel-container');
            if (!container) return false;

            const titleSlider = container.querySelector('.title-slider');
            return titleSlider !== null;
        } catch (error) {
            console.warn('Error checking for title slider:', error);
            return false;
        }
    }

    updateTitleSlider(sliderId: string, slideIndex: number): void {
        try {
            const sliderElement = document.querySelector(`[data-slider-id="${sliderId}"]`);
            if (!sliderElement) return;

            const container = sliderElement.closest('.media-slider-carousel-container');
            if (!container) return;

            const titleSlider = container.querySelector('.title-slider');
            if (!titleSlider) return;

            const titleSlides = titleSlider.querySelectorAll<HTMLElement>('.title-slide');
            const totalSlides = titleSlides.length;

            if (totalSlides === 0) return;

            // Remove active class from all slides
            titleSlides.forEach((slide: HTMLElement) => {
                slide.classList.remove('active');
            });

            // Add active class to current slide
            const currentIndex = slideIndex % totalSlides;
            if (titleSlides[currentIndex]) {
                titleSlides[currentIndex].classList.add('active');
            }
        } catch (error) {
            console.warn('Error updating title slider:', error);
        }
    }

    handleVideoSlides(sliderElement: HTMLElement, swiper: SwiperType): void {
        const videoSlides = sliderElement.querySelectorAll<HTMLVideoElement>('.swiper-slide video');

        videoSlides.forEach((video: HTMLVideoElement) => {
            // Pause video when slide changes
            swiper.on('slideChange', function () {
                video.pause();
            });

            // Auto-play video when slide becomes active
            swiper.on('slideChangeTransitionEnd', function () {
                const activeSlide = sliderElement.querySelector('.swiper-slide-active');
                const activeVideo = activeSlide?.querySelector<HTMLVideoElement>('video');

                if (activeVideo && activeVideo === video) {
                    // Optional: auto-play video in active slide
                    // activeVideo.play();
                }
            });
        });
    }

    pauseAllVideos(): void {
        document.querySelectorAll<HTMLVideoElement>('.swiper-slide video').forEach((video: HTMLVideoElement) => {
            video.pause();
        });
    }

    // Destroy specific slider
    destroySlider(sliderId: string): void {
        if (this.swipers.has(sliderId)) {
            this.swipers.get(sliderId)?.destroy(true, true);
            this.swipers.delete(sliderId);
        }
    }

    // Destroy all sliders
    destroyAll(): void {
        this.swipers.forEach((swiper: SwiperType) => {
            swiper.destroy(true, true);
        });
        this.swipers.clear();
    }
}

// Initialize the media slider carousel
new MediaSliderCarousel();

// Export for potential external use
window.MediaSliderCarousel = MediaSliderCarousel;
