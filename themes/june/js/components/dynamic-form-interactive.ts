/**
 * Dynamic Form Interactive Component
 * Handles background image switching, height synchronization, and Livewire event handling
 */

interface OptionsMapping {
    [key: string]: string;
}

/**
 * Normalize asset URL to handle various formats
 */
function normalizeAssetUrl(url: string, baseUrl: string): string {
    if (!url) return '';

    // Already absolute URL (http://, https://, //)
    if (/^(https?:)?\/\//.test(url)) {
        return url;
    }

    // Normalize base URL (remove trailing slash)
    const base = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;

    // Remove leading slash from path to avoid double slashes
    const path = url.startsWith('/') ? url.slice(1) : url;

    // Prepend base URL to path (handles both root-relative and relative paths)
    return `${base}/${path}`;
}

/**
 * Get value from URL query parameter
 */
function getUrlParameter(name: string): string | null {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/**
 * Normalize vehicle name for matching (handles hyphens/spaces)
 */
function normalizeVehicleName(name: string): string {
    return name.trim().toLowerCase();
}

/**
 * Find matching option in mapping (handles variations)
 */
function findMatchingOption(value: string, mapping: OptionsMapping): string | null {
    const normalizedValue = normalizeVehicleName(value);

    // Try exact match first
    for (const [key, url] of Object.entries(mapping)) {
        if (normalizeVehicleName(key) === normalizedValue) {
            return url;
        }
    }

    // Try with hyphens/spaces variations
    const valueWithSpaces = normalizedValue.replace(/-/g, ' ');
    const valueWithHyphens = normalizedValue.replace(/\s+/g, '-');

    for (const [key, url] of Object.entries(mapping)) {
        const normalizedKey = normalizeVehicleName(key);
        if (normalizedKey === valueWithSpaces || normalizedKey === valueWithHyphens) {
            return url;
        }
    }

    return null;
}

/**
 * Update background image
 */
function updateBackgroundImage(container: HTMLElement, imageUrl: string, baseUrl: string): void {
    const backgroundImageEl = container.querySelector<HTMLElement>('.dynamic-form-background-image');
    if (!backgroundImageEl) return;

    const normalizedUrl = normalizeAssetUrl(imageUrl, baseUrl);

    // Don't update if it's already the same image
    const currentBg = backgroundImageEl.style.backgroundImage;
    if (currentBg && currentBg.includes(normalizedUrl)) {
        return;
    }

    // Create new image to preload
    const img = new Image();
    img.onload = () => {
        // Set background image and activate
        backgroundImageEl.style.backgroundImage = `url(${normalizedUrl})`;
        backgroundImageEl.classList.add('active');
    };
    img.onerror = () => {
        console.error('[DynamicFormInteractive] Failed to load image:', normalizedUrl);
    };
    img.src = normalizedUrl;
}

/**
 * Sync height between background and form container
 */
function syncHeight(container: HTMLElement): void {
    const backgroundEl = container.querySelector<HTMLElement>('.dynamic-form-background');
    const formContainerEl = container.querySelector<HTMLElement>('.dynamic-form-container');

    if (!backgroundEl || !formContainerEl) return;

    const updateHeight = () => {
        const formHeight = formContainerEl.offsetHeight;
        if (formHeight > 0) {
            backgroundEl.style.height = `${formHeight}px`;
        }
    };

    // Use ResizeObserver if available
    if (typeof ResizeObserver !== 'undefined') {
        const observer = new ResizeObserver(() => {
            updateHeight();
        });
        observer.observe(formContainerEl);
    } else {
        // Fallback to window resize
        window.addEventListener('resize', updateHeight);
        // Also update on initial load
        setTimeout(updateHeight, 100);
    }

    // Initial height sync
    updateHeight();
}

/**
 * Get random image URL from options mapping
 */
function getRandomImageUrl(mapping: OptionsMapping): string | null {
    const urls = Object.values(mapping).filter((url) => url && url.trim());
    if (urls.length === 0) return null;
    const randomIndex = Math.floor(Math.random() * urls.length);
    return urls[randomIndex];
}

/**
 * Initialize background from URL parameter or random selection
 */
function initializeFromUrl(container: HTMLElement, baseUrl: string, triggerFieldName: string): void {
    const urlValue = getUrlParameter(triggerFieldName);

    // Find the hidden field with options mapping
    const hiddenField = container.querySelector<HTMLInputElement>('input[type="hidden"][data-options-mapping]');
    if (!hiddenField) {
        // Try again after a short delay in case form hasn't loaded yet
        setTimeout(() => {
            const delayedField = container.querySelector<HTMLInputElement>(
                'input[type="hidden"][data-options-mapping]'
            );
            if (delayedField) {
                try {
                    const mapping: OptionsMapping = JSON.parse(
                        delayedField.getAttribute('data-options-mapping') || '{}'
                    );
                    let imageUrl: string | null = null;

                    if (urlValue) {
                        // Use URL parameter if available
                        imageUrl = findMatchingOption(urlValue, mapping);
                    } else {
                        // Pick random image if no URL parameter
                        imageUrl = getRandomImageUrl(mapping);
                    }

                    if (imageUrl) {
                        updateBackgroundImage(container, imageUrl, baseUrl);
                    }
                } catch (error) {
                    console.error('[DynamicFormInteractive] Failed to parse options mapping:', error);
                }
            }
        }, 200);
        return;
    }

    try {
        const mapping: OptionsMapping = JSON.parse(hiddenField.getAttribute('data-options-mapping') || '{}');
        let imageUrl: string | null = null;

        if (urlValue) {
            // Use URL parameter if available
            imageUrl = findMatchingOption(urlValue, mapping);
        } else {
            // Pick random image if no URL parameter
            imageUrl = getRandomImageUrl(mapping);
        }

        if (imageUrl) {
            updateBackgroundImage(container, imageUrl, baseUrl);
        }
    } catch (error) {
        console.error('[DynamicFormInteractive] Failed to parse options mapping:', error);
    }
}

/**
 * Setup form field change listeners
 */
function setupFormListeners(container: HTMLElement, baseUrl: string, triggerFieldName: string): void {
    // Find the trigger field (select, radio, etc.)
    // Try multiple selectors to find the field
    const triggerField =
        container.querySelector<HTMLSelectElement>(`select[data-field-name="${triggerFieldName}"]`) ||
        container.querySelector<HTMLSelectElement>(`select[name="${triggerFieldName}"]`) ||
        container.querySelector<HTMLSelectElement>(`select[id="${triggerFieldName}"]`);
    if (!triggerField) {
        console.warn(`[DynamicFormInteractive] Trigger field "${triggerFieldName}" not found`);
        return;
    }

    // Find the hidden field with options mapping
    const hiddenField = container.querySelector<HTMLInputElement>('input[type="hidden"][data-options-mapping]');
    if (!hiddenField) return;

    let mapping: OptionsMapping = {};
    try {
        mapping = JSON.parse(hiddenField.getAttribute('data-options-mapping') || '{}');
    } catch (error) {
        console.error('[DynamicFormInteractive] Failed to parse options mapping:', error);
        return;
    }

    // Listen for changes
    triggerField.addEventListener('change', (e) => {
        const target = e.target as HTMLSelectElement;
        const selectedValue = target.value;
        if (!selectedValue) return;

        const imageUrl = findMatchingOption(selectedValue, mapping);
        if (imageUrl) {
            updateBackgroundImage(container, imageUrl, baseUrl);
        }
    });

    // Also listen for Livewire updates
    container.addEventListener('livewire:update', () => {
        const selectedValue = triggerField.value;
        if (selectedValue) {
            const imageUrl = findMatchingOption(selectedValue, mapping);
            if (imageUrl) {
                updateBackgroundImage(container, imageUrl, baseUrl);
            }
        }
    });
}

/**
 * Initialize component
 */
function initializeComponent(container: HTMLElement): void {
    const baseUrl = container.getAttribute('data-asset-base-url') || '/assets/';
    const triggerFieldName = container.getAttribute('data-trigger-field') || 'vehicle_interest';

    // Sync height first (needs to be ready)
    syncHeight(container);

    // Initialize from URL parameter
    initializeFromUrl(container, baseUrl, triggerFieldName);

    // Setup form listeners (with delay to ensure form is rendered)
    setTimeout(() => {
        setupFormListeners(container, baseUrl, triggerFieldName);
    }, 100);
}

/**
 * Initialize all dynamic form interactive components
 */
function initializeAllComponents(): void {
    const containers = document.querySelectorAll<HTMLElement>('.dynamic-form-interactive');
    containers.forEach((container) => {
        if (container.hasAttribute('data-initialized')) return;
        container.setAttribute('data-initialized', 'true');
        initializeComponent(container);
    });
}

/**
 * Setup Livewire event listeners
 */
function setupLivewireListeners(): void {
    // Re-initialize on Livewire load
    document.addEventListener('livewire:load', () => {
        setTimeout(initializeAllComponents, 100);
    });

    // Re-initialize on Livewire update
    document.addEventListener('livewire:update', () => {
        setTimeout(initializeAllComponents, 100);
    });

    // Re-initialize on Livewire navigation (SPA)
    document.addEventListener('livewire:navigated', () => {
        setTimeout(initializeAllComponents, 100);
    });
}

/**
 * Main initialization
 */
function init(): void {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initializeAllComponents, 100);
        });
    } else {
        setTimeout(initializeAllComponents, 100);
    }

    setupLivewireListeners();
}

init();

export { initializeComponent, initializeAllComponents };
