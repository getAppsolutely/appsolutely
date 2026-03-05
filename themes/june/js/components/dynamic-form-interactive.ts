/**
 * Dynamic Form Interactive Component
 * Handles background image switching, height synchronization, and Livewire event handling
 */

import { buildUrl } from '../utils/url';

/** Delay (ms) to wait for Livewire to render form elements before initializing */
const LIVEWIRE_RENDER_DELAY_MS = 100;
/** Delay (ms) to retry when hidden field with options mapping is not yet in DOM */
const HIDDEN_FIELD_POLL_DELAY_MS = 200;
/** Delay (ms) for ResizeObserver fallback when measuring form height */
const FALLBACK_RESIZE_DELAY_MS = 100;

interface OptionsMapping {
    [key: string]: string;
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
    const backgroundImageEl = container.querySelector<HTMLElement>('.dynamic-form-interactive__background-image');
    if (!backgroundImageEl) return;

    const normalizedUrl = buildUrl(imageUrl, baseUrl);

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

const RESIZE_CLEANUP = new WeakMap<HTMLElement, () => void>();

/**
 * Sync height between background and form container.
 * Cleans up observers/listeners when container is removed from DOM.
 */
function syncHeight(container: HTMLElement): void {
    const backgroundEl = container.querySelector<HTMLElement>('.dynamic-form-interactive__background');
    const formContainerEl = container.querySelector<HTMLElement>('.dynamic-form-interactive__container');

    if (!backgroundEl || !formContainerEl) return;

    // Clean up previous sync for this container if re-initialized
    RESIZE_CLEANUP.get(container)?.();

    const updateHeight = (): void => {
        if (!document.contains(formContainerEl)) {
            RESIZE_CLEANUP.get(container)?.();
            return;
        }
        const formHeight = formContainerEl.offsetHeight;
        if (formHeight > 0) {
            backgroundEl.style.height = `${formHeight}px`;
        }
    };

    let cleanup: () => void;

    if (typeof ResizeObserver !== 'undefined') {
        const observer = new ResizeObserver(updateHeight);
        observer.observe(formContainerEl);
        cleanup = () => observer.disconnect();
    } else {
        window.addEventListener('resize', updateHeight);
        setTimeout(updateHeight, FALLBACK_RESIZE_DELAY_MS);
        cleanup = () => window.removeEventListener('resize', updateHeight);
    }

    RESIZE_CLEANUP.set(container, cleanup);
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
 * Apply initial background image from URL parameter or random selection
 */
function applyInitialImage(
    container: HTMLElement,
    baseUrl: string,
    urlValue: string | null,
    mapping: OptionsMapping
): void {
    const imageUrl = urlValue ? findMatchingOption(urlValue, mapping) : getRandomImageUrl(mapping);
    if (imageUrl) {
        updateBackgroundImage(container, imageUrl, baseUrl);
    }
}

/**
 * Initialize background from URL parameter or random selection
 */
function initializeFromUrl(container: HTMLElement, baseUrl: string, triggerFieldName: string): void {
    const urlValue = getUrlParameter(triggerFieldName);

    const hiddenField = container.querySelector<HTMLInputElement>('input[type="hidden"][data-options-mapping]');
    if (!hiddenField) {
        setTimeout(() => {
            const delayedField = container.querySelector<HTMLInputElement>(
                'input[type="hidden"][data-options-mapping]'
            );
            if (delayedField) {
                try {
                    const mapping: OptionsMapping = JSON.parse(
                        delayedField.getAttribute('data-options-mapping') || '{}'
                    );
                    applyInitialImage(container, baseUrl, urlValue, mapping);
                } catch (error) {
                    console.error('[DynamicFormInteractive] Failed to parse options mapping:', error);
                }
            }
        }, HIDDEN_FIELD_POLL_DELAY_MS);
        return;
    }

    try {
        const mapping: OptionsMapping = JSON.parse(hiddenField.getAttribute('data-options-mapping') || '{}');
        applyInitialImage(container, baseUrl, urlValue, mapping);
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
    }, LIVEWIRE_RENDER_DELAY_MS);
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

let livewireListenersSetup = false;

/**
 * Setup Livewire event listeners (once)
 */
function setupLivewireListeners(): void {
    if (livewireListenersSetup) return;
    livewireListenersSetup = true;

    document.addEventListener('livewire:load', () => {
        setTimeout(initializeAllComponents, LIVEWIRE_RENDER_DELAY_MS);
    });

    document.addEventListener('livewire:update', () => {
        setTimeout(initializeAllComponents, LIVEWIRE_RENDER_DELAY_MS);
    });

    document.addEventListener('livewire:navigated', () => {
        setTimeout(initializeAllComponents, LIVEWIRE_RENDER_DELAY_MS);
    });
}

/**
 * Main initialization
 */
export function init(): void {
    setTimeout(initializeAllComponents, LIVEWIRE_RENDER_DELAY_MS);
    setupLivewireListeners();
}

export { initializeComponent, initializeAllComponents };
