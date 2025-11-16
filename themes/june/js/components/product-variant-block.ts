/**
 * Product Variant Block Component
 * Handles image switching and thumbnail interactions
 */

class ProductVariantBlock {
    constructor() {
        this.init();
    }

    init(): void {
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeThumbnails();
        });

        // Handle Livewire re-renders
        document.addEventListener('livewire:navigated', () => {
            this.initializeThumbnails();
        });

        // Handle component updates
        document.addEventListener('livewire:updated', () => {
            this.initializeThumbnails();
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
