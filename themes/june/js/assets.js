/**
 * Asset Imports for June Theme
 * 
 * This file imports all assets (images, fonts, etc.) from the theme folders to ensure
 * they are included in the Vite build output and available via Vite::asset()
 */

// Import all images from the images folder
import '../images/logo.webp';
import '../images/logo-dark.webp';

// Export asset paths for use in components if needed
export const assets = {
    images: {
        logo: '/themes/june/images/logo.webp',
        logoDark: '/themes/june/images/logo-dark.webp',
    },
};

// Make assets available globally for debugging
if (typeof window !== 'undefined') {
    window.juneAssets = assets;
} 