/**
 * Product Variant Block Component
 *
 * This component is now minimal since Alpine.js handles all variant switching
 * and image interactions client-side without server roundtrips.
 *
 * The TypeScript is kept only for future enhancements if needed.
 * All reactivity is now handled by Alpine.js in the Blade template.
 */

class ProductVariantBlock {
    constructor() {
        this.init();
    }

    init(): void {
        // Alpine.js handles all interactions now
        // This file is kept for future enhancements or custom behaviors
        console.log('ProductVariantBlock: Using Alpine.js for client-side reactivity');
    }
}

// Initialize the component
export default new ProductVariantBlock();
