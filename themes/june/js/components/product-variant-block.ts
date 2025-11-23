/**
 * Product Variant Block Component
 * Handles image switching and thumbnail interactions
 * Includes iOS-specific touch event handling for variant tabs
 */

class ProductVariantBlock {
    constructor() {
        this.init();
    }

    init(): void {
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeVariantTabs();
            this.initializeThumbnails();
        });

        // Handle Livewire re-renders
        document.addEventListener('livewire:navigated', () => {
            this.initializeVariantTabs();
            this.initializeThumbnails();
        });

        // Handle component updates
        document.addEventListener('livewire:updated', () => {
            this.initializeVariantTabs();
            this.initializeThumbnails();
        });
    }

    /**
     * Initialize variant tabs with iOS-compatible touch event handling
     */
    private initializeVariantTabs(): void {
        const variantTabButtons = document.querySelectorAll('.variant-tabs .nav-link[wire\\:click]');

        variantTabButtons.forEach((button) => {
            // Check if we've already added touch handlers to avoid duplicates
            if ((button as HTMLElement).dataset.touchHandlerAdded === 'true') {
                return;
            }

            // Mark as processed
            (button as HTMLElement).dataset.touchHandlerAdded = 'true';

            let touchStartTime = 0;
            let touchStartTarget: HTMLElement | null = null;
            let clickFired = false;
            let touchTimeout: ReturnType<typeof setTimeout> | null = null;

            // Track if click event fires naturally (to avoid double-firing)
            const clickHandler = () => {
                clickFired = true;
                if (touchTimeout) {
                    clearTimeout(touchTimeout);
                    touchTimeout = null;
                }
            };
            button.addEventListener('click', clickHandler, { passive: true });

            // Add touchstart handler for iOS Safari compatibility
            button.addEventListener(
                'touchstart',
                (e) => {
                    touchStartTime = Date.now();
                    touchStartTarget = e.target as HTMLElement;
                    clickFired = false;

                    // Clear any pending timeout
                    if (touchTimeout) {
                        clearTimeout(touchTimeout);
                        touchTimeout = null;
                    }
                },
                { passive: true }
            );

            // Add touchend handler to trigger click on iOS
            button.addEventListener(
                'touchend',
                (e) => {
                    const touchEndTime = Date.now();
                    const touchDuration = touchEndTime - touchStartTime;

                    // Only trigger if it was a quick tap (not a scroll)
                    if (touchDuration < 300 && touchDuration > 0 && touchStartTarget === e.target) {
                        // Small delay to check if click event fires naturally
                        touchTimeout = setTimeout(() => {
                            if (!clickFired) {
                                // Click didn't fire naturally on iOS, trigger it manually
                                const clickEvent = new MouseEvent('click', {
                                    bubbles: true,
                                    cancelable: true,
                                    view: window,
                                    detail: 1,
                                });

                                // Dispatch the click event to trigger Livewire's wire:click
                                button.dispatchEvent(clickEvent);
                            }
                            touchTimeout = null;
                        }, 100);
                    }
                },
                { passive: true }
            );
        });
    }

    private initializeThumbnails(): void {
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        const mainImage = document.querySelector('.product-main-image') as HTMLImageElement;

        if (!mainImage) {
            return;
        }

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                const thumbnailImg = thumbnail as HTMLImageElement;
                if (thumbnailImg.src) {
                    // Swap main image with clicked thumbnail
                    const tempSrc = mainImage.src;
                    mainImage.src = thumbnailImg.src;
                    thumbnailImg.src = tempSrc;

                    // Add fade effect
                    mainImage.style.opacity = '0';
                    setTimeout(() => {
                        mainImage.style.opacity = '1';
                    }, 150);
                }
            });
        });
    }
}

// Initialize the component
export default new ProductVariantBlock();
