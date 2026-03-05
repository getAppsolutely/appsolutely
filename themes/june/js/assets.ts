/**
 * Asset Imports for Theme
 *
 * This file imports all assets (images, fonts, etc.) from the theme folders to ensure
 * they are included in the Vite build output and available via Vite::asset()
 */

import type { AssetPaths } from './types';

// Import images – Vite resolves these to built URLs
import comingImg from '../images/coming.png';
import rednoteImg from '../images/rednote.svg';

// Export asset paths for use in components if needed
export const assets: AssetPaths = {
    images: {
        coming: comingImg,
        rednote: rednoteImg,
    },
};

// Make assets available globally for debugging
if (typeof window !== 'undefined') {
    window.assets = assets;
}
