/**
 * Product Variant Block - Native JavaScript Fallback
 *
 * This module provides a fallback for tab switching when Alpine.js fails to initialize.
 * The primary UI is handled by inline Alpine.js in the Blade template.
 * This module activates only if Alpine doesn't initialize properly.
 */

interface ProductVariant {
    name: string;
    price?: number | string;
    specs?: string[];
    colors?: ProductColor[];
}

interface ProductColor {
    name: string;
    code: string;
    images?: string[];
}

interface ProductData {
    name: string;
    common?: Record<string, string>;
    variants: ProductVariant[];
}

/**
 * Extracts product data from a component's script[data-product] element
 */
function extractProductData(container: HTMLElement): ProductData | null {
    const dataEl = container.querySelector('script[data-product]');
    if (!dataEl?.textContent) return null;

    try {
        return JSON.parse(dataEl.textContent.trim()) as ProductData;
    } catch (e) {
        console.error('[ProductVariantBlock] Failed to parse product data:', e);
        return null;
    }
}

/**
 * Native JavaScript fallback for tab switching
 * This works even when Alpine.js fails to initialize
 */
function initializeNativeFallback(container: HTMLElement): void {
    const productData = extractProductData(container);
    if (!productData?.variants?.length) return;

    let currentVariantIndex = 0;
    let currentColorIndex = 0;

    const tabs = container.querySelectorAll<HTMLButtonElement>('.variant-tabs .nav-link');

    function updateDisplay(variantIndex: number, colorIndex: number = 0): void {
        const variant = productData?.variants?.[variantIndex];
        if (!variant) return;

        const color = variant.colors?.[colorIndex];

        // Update tab active states
        tabs.forEach((tab, idx) => {
            tab.classList.toggle('active', idx === variantIndex);
            tab.setAttribute('aria-selected', idx === variantIndex ? 'true' : 'false');
        });

        // Update main image
        const mainImage = container.querySelector<HTMLImageElement>('.product-main-image');
        if (mainImage && color?.images?.[0]) {
            mainImage.src = color.images[0];
            mainImage.alt = color.name || 'Product Image';
        }

        // Update variant name
        const variantName = container.querySelector<HTMLElement>('.variant-info h2');
        if (variantName) {
            variantName.textContent = variant.name || 'Variant';
        }

        // Update price
        const priceEl = container.querySelector<HTMLElement>('.price-section span:last-child');
        if (priceEl && variant.price !== undefined && variant.price !== null) {
            const price = typeof variant.price === 'number' ? variant.price.toLocaleString() : variant.price;
            priceEl.textContent = String(price);
        }

        // Update specs
        const specsList = container.querySelector<HTMLElement>('.specifications-section .list-group');
        if (specsList && variant?.specs) {
            specsList.innerHTML = variant.specs.map((spec) => `<li class="list-group-item">${spec}</li>`).join('');
        }

        // Update color options
        const colorOptions = container.querySelectorAll<HTMLButtonElement>('.color-selection .color-option');
        colorOptions.forEach((option, idx) => {
            option.classList.toggle('active', idx === colorIndex);
            const checkIcon = option.querySelector('.fa-check');
            if (checkIcon) {
                (checkIcon as HTMLElement).style.display = idx === colorIndex ? 'block' : 'none';
            }
        });

        // Update selected color name
        const colorName = container.querySelector<HTMLElement>('.color-selection p span');
        if (colorName && color) {
            colorName.textContent = color.name || 'Unnamed Color';
        }

        currentVariantIndex = variantIndex;
        currentColorIndex = colorIndex;
    }

    // Event delegation for clicks
    container.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;

        // Handle variant tab clicks
        const tabButton = target.closest<HTMLButtonElement>('.variant-tabs .nav-link');
        if (tabButton) {
            e.preventDefault();
            e.stopPropagation();
            const tabIndex = Array.from(tabs).indexOf(tabButton);
            if (tabIndex >= 0 && tabIndex !== currentVariantIndex) {
                updateDisplay(tabIndex, 0);
            }
        }

        // Handle color selection clicks
        const colorButton = target.closest<HTMLButtonElement>('.color-selection .color-option');
        if (colorButton) {
            e.preventDefault();
            e.stopPropagation();
            const colorOptions = container.querySelectorAll<HTMLButtonElement>('.color-selection .color-option');
            const colorIndex = Array.from(colorOptions).indexOf(colorButton);
            if (colorIndex >= 0 && colorIndex !== currentColorIndex) {
                updateDisplay(currentVariantIndex, colorIndex);
            }
        }
    });

    container.setAttribute('data-native-fallback', 'true');
}

/**
 * Check if Alpine.js has initialized a component
 */
function hasAlpineInitialized(element: HTMLElement): boolean {
    const alpineRoot = element.querySelector('[x-data]');
    return !!(alpineRoot && (alpineRoot as unknown as { _x_dataStack?: unknown[] })._x_dataStack?.length);
}

/**
 * Initialize fallback for all product variant blocks if Alpine fails
 */
function initializeAllBlocks(): void {
    document.querySelectorAll<HTMLElement>('.product-variant-block').forEach((block) => {
        if (block.hasAttribute('data-native-fallback')) return;

        // Give Alpine time to initialize, then check if fallback is needed
        setTimeout(() => {
            if (!hasAlpineInitialized(block)) {
                console.warn('[ProductVariantBlock] Alpine not initialized, using native fallback');
                initializeNativeFallback(block);
            }
        }, 500);
    });
}

/**
 * Setup Livewire event listeners for SPA navigation
 */
function setupLivewireListeners(): void {
    document.addEventListener('livewire:navigated', () => {
        setTimeout(initializeAllBlocks, 100);
    });

    document.addEventListener('livewire:init', () => {
        setTimeout(initializeAllBlocks, 100);
    });
}

/**
 * Main initialization
 */
function init(): void {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initializeAllBlocks, 100);
        });
    } else {
        setTimeout(initializeAllBlocks, 100);
    }

    setupLivewireListeners();
}

init();

export { initializeNativeFallback, initializeAllBlocks };
