/**
 * Product Variant Block Component
 *
 * Alpine.js handles all variant switching and image interactions client-side.
 * This TypeScript provides debugging utilities and Livewire integration hooks.
 */

interface ProductVariantDebugData {
    product: unknown;
    selectedVariantIndex: number;
    selectedColorIndex: number;
    currentVariant: unknown;
    currentColor: unknown;
}

class ProductVariantBlock {
    private debugEnabled: boolean = false;

    constructor() {
        this.init();
    }

    init(): void {
        // Enable debug mode based on URL param or localStorage
        this.debugEnabled =
            window.location.search.includes('debug=1') || window.localStorage.getItem('productVariantDebug') === '1';

        if (this.debugEnabled) {
            console.log('[ProductVariantBlock] Debug mode enabled');
            this.setupDebugHelpers();
        }

        // Listen for Livewire events to help with debugging
        this.setupLivewireListeners();
    }

    private setupDebugHelpers(): void {
        // Add global debug function
        (window as unknown as Record<string, unknown>).debugProductVariant = () => {
            const blocks = document.querySelectorAll('.product-variant-block');
            blocks.forEach((block, index) => {
                const alpineData = (block as HTMLElement & { _x_dataStack?: unknown[] })._x_dataStack?.[0] as
                    | ProductVariantDebugData
                    | undefined;
                console.log(`[ProductVariantBlock] Block #${index}`, {
                    product: alpineData?.product,
                    selectedVariantIndex: alpineData?.selectedVariantIndex,
                    selectedColorIndex: alpineData?.selectedColorIndex,
                    currentVariant: alpineData?.currentVariant,
                    currentColor: alpineData?.currentColor,
                });
            });
        };

        console.log('[ProductVariantBlock] Debug helper available: window.debugProductVariant()');
    }

    private setupLivewireListeners(): void {
        // Handle Livewire navigate events (for SPA navigation)
        document.addEventListener('livewire:navigated', () => {
            if (this.debugEnabled) {
                console.log('[ProductVariantBlock] Livewire navigated - checking Alpine state');
            }
        });

        // Handle Livewire morphing which might affect Alpine state
        document.addEventListener('livewire:morph', (event: Event) => {
            if (this.debugEnabled) {
                console.log('[ProductVariantBlock] Livewire morph event:', event);
            }
        });
    }
}

// Initialize the component
export default new ProductVariantBlock();
