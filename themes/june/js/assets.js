/**
 * Asset Imports for Theme
 *
 * This file imports all assets (images, fonts, etc.) from the theme folders to ensure
 * they are included in the Vite build output and available via Vite::asset()
 */

// Import images that should be emitted by Vite
import '../images/coming.png';

// Export asset paths for use in components if needed
export const assets = {
    images: {
        coming: '../images/coming.png',
    },
};

// Make assets available globally for debugging
if (typeof window !== 'undefined') {
    window.assets = assets;
}
