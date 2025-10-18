/**
 * Lazy Loading Component
 *
 * Provides consistent lazy loading functionality using vanilla-lazyload
 * for images, videos, and background images across the June theme.
 */

import LazyLoad from 'vanilla-lazyload';

interface LazyLoadOptions {
    rootMargin?: string;
    threshold?: number;
    enableAuto?: boolean;
    loadingClass?: string;
    loadedClass?: string;
    errorClass?: string;
}

class LazyLoadingManager {
    private lazyLoadInstance: any = null;
    private defaultOptions: LazyLoadOptions = {
        rootMargin: '50px 0px',
        threshold: 0.1,
        enableAuto: true,
        loadingClass: 'lazy-loading',
        loadedClass: 'lazy-loaded',
        errorClass: 'lazy-error',
    };

    constructor() {
        this.init();
    }

    /**
     * Initialize lazy loading with default options
     */
    init(options: Partial<LazyLoadOptions> = {}): void {
        const config = { ...this.defaultOptions, ...options };

        this.lazyLoadInstance = new LazyLoad({
            elements_selector: '.lazy',
            root_margin: config.rootMargin,
            threshold: config.threshold,
            enableAuto: config.enableAuto,
            class_loading: config.loadingClass,
            class_loaded: config.loadedClass,
            class_error: config.errorClass,
            callback_loaded: (element: HTMLElement) => {
                this.onElementLoaded(element);
            },
            callback_error: (element: HTMLElement) => {
                this.onElementError(element);
            },
            // Handle background images
            callback_enter: (element: HTMLElement) => {
                if (element.classList.contains('lazy-bg')) {
                    const bgSrc = element.getAttribute('data-bg');
                    if (bgSrc) {
                        element.style.backgroundImage = `url(${bgSrc})`;
                    }
                }
            },
        } as any);

        // Make instance available globally for debugging
        if (typeof window !== 'undefined') {
            (window as any).lazyLoadInstance = this.lazyLoadInstance;
        }
    }

    /**
     * Update lazy loading for dynamically added content
     */
    update(): void {
        if (this.lazyLoadInstance) {
            this.lazyLoadInstance.update();
        }
    }

    /**
     * Destroy lazy loading instance
     */
    destroy(): void {
        if (this.lazyLoadInstance) {
            this.lazyLoadInstance.destroy();
            this.lazyLoadInstance = null;
        }
    }

    /**
     * Handle element loaded callback
     */
    private onElementLoaded(element: HTMLElement): void {
        // Add fade-in animation
        element.style.opacity = '0';
        element.style.transition = 'opacity 0.3s ease-in-out';

        // Trigger fade-in after a short delay
        setTimeout(() => {
            element.style.opacity = '1';
        }, 50);

        // Dispatch custom event for additional handling
        element.dispatchEvent(
            new window.CustomEvent('lazyLoaded', {
                detail: { element },
            })
        );
    }

    /**
     * Handle element error callback
     */
    private onElementError(element: HTMLElement): void {
        console.warn('Lazy loading failed for element:', element);

        // Dispatch custom event for error handling
        element.dispatchEvent(
            new window.CustomEvent('lazyError', {
                detail: { element },
            })
        );
    }

    /**
     * Create lazy image element with proper attributes
     */
    static createLazyImage(src: string, alt: string = '', className: string = ''): string {
        return `
            <img 
                class="lazy ${className}" 
                data-src="${src}" 
                alt="${alt}"
                loading="lazy"
            />
        `;
    }

    /**
     * Create lazy video element with proper attributes
     */
    static createLazyVideo(src: string, poster?: string, className: string = ''): string {
        const posterAttr = poster ? `poster="${poster}"` : '';
        return `
            <video 
                class="lazy ${className}" 
                data-src="${src}" 
                ${posterAttr}
                controls
                preload="none"
            >
                Your browser does not support the video tag.
            </video>
        `;
    }

    /**
     * Create lazy background image element
     */
    static createLazyBackground(src: string, className: string = ''): string {
        return `
            <div 
                class="lazy lazy-bg ${className}" 
                data-bg="${src}"
            ></div>
        `;
    }
}

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const lazyManager = new LazyLoadingManager();

    // Make manager available globally for dynamic content
    if (typeof window !== 'undefined') {
        (window as any).lazyManager = lazyManager;
    }
});

// Export for use in other components
export default LazyLoadingManager;
