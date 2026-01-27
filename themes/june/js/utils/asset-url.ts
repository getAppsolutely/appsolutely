/**
 * Asset URL Utility
 *
 * This utility provides a JavaScript equivalent of Laravel's asset_url() helper.
 * It reads the asset base URL and build hash from meta tags and constructs
 * full asset URLs on the frontend.
 */

let assetBaseUrl: string | null = null;
let buildHash: string | null = null;

/**
 * Initialize asset URL configuration from meta tags
 */
function initAssetConfig(): void {
    if (assetBaseUrl !== null) return; // Already initialized

    const baseUrlMeta = document.head.querySelector<HTMLMetaElement>('meta[name="asset-base-url"]');
    const hashMeta = document.head.querySelector<HTMLMetaElement>('meta[name="asset-build-hash"]');

    assetBaseUrl = baseUrlMeta?.content || 'assets/';
    buildHash = hashMeta?.content || '';
}

/**
 * Construct a full asset URL from a relative path
 *
 * @param uri - The relative path to the asset (e.g., 'images/photo.jpg')
 * @param withHash - Whether to append the build hash for cache busting (default: true)
 * @returns The full asset URL
 *
 * @example
 * asset_url('images/photo.jpg') // => 'https://cdn.example.com/assets/images/photo.jpg?v=abc123'
 * asset_url('images/photo.jpg', false) // => 'https://cdn.example.com/assets/images/photo.jpg'
 */
export function asset_url(uri: string | null | undefined, withHash: boolean = true): string {
    initAssetConfig();

    if (!uri || uri.trim() === '') {
        return '';
    }

    // If it's already a full URL (http:// or https://), return as-is
    if (uri.startsWith('http://') || uri.startsWith('https://')) {
        return uri;
    }

    const baseUrl = assetBaseUrl || 'assets/';
    const hash = withHash && buildHash ? `?v=${buildHash}` : '';

    // Handle base URL ending with slash and uri starting with slash
    const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
    const cleanUri = uri.startsWith('/') ? uri : `/${uri}`;

    return `${cleanBaseUrl}${cleanUri}${hash}`;
}

/**
 * Get the asset base URL
 */
export function getAssetBaseUrl(): string {
    initAssetConfig();
    return assetBaseUrl || 'assets/';
}

/**
 * Get the build hash
 */
export function getBuildHash(): string {
    initAssetConfig();
    return buildHash || '';
}

// Make asset_url available globally on window object
declare global {
    interface Window {
        asset_url: typeof asset_url;
        getAssetBaseUrl: typeof getAssetBaseUrl;
        getBuildHash: typeof getBuildHash;
    }
}

if (typeof window !== 'undefined') {
    window.asset_url = asset_url;
    window.getAssetBaseUrl = getAssetBaseUrl;
    window.getBuildHash = getBuildHash;
}
