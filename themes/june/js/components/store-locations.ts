/**
 * Store Locations Component
 * Provides smart map opening functionality based on device platform
 */

/**
 * Opens a map application intelligently based on the user's device and platform.
 *
 * Platform-specific behavior:
 * - iOS/iPadOS/macOS: Attempts to open Google Maps app first (if installed),
 *   falls back to Apple Maps after 1.2 seconds if Google Maps is not available
 * - Android: Uses geo:// protocol to open the default maps app
 * - Desktop/Other: Opens Google Maps in the web browser
 *
 * @param lat - Latitude coordinate of the location
 * @param lng - Longitude coordinate of the location
 * @param name - Optional name/label for the location (defaults to coordinates if not provided)
 *
 * @example
 * // Open map with coordinates and location name
 * openSmartMap(31.2304, 121.4737, 'Shanghai People\'s Square');
 *
 * // Open map with coordinates only
 * openSmartMap(31.2304, 121.4737);
 */
export function openSmartMap(lat: number, lng: number, name: string = ''): void {
    const ua = navigator.userAgent.toLowerCase();
    const encodedName = encodeURIComponent(name || `${lat},${lng}`);
    const appleURL = `http://maps.apple.com/?ll=${lat},${lng}&q=${encodedName}`;
    const googleAppURL = `comgooglemaps://?q=${encodedName}&center=${lat},${lng}&zoom=14`;
    const googleWebURL = `https://www.google.com/maps?q=${encodedName}&ll=${lat},${lng}`;

    if (/iphone|ipad|mac/.test(ua)) {
        // iOS/iPadOS/macOS: Try Google Maps app first, fallback to Apple Maps
        const now = Date.now();
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = googleAppURL;
        document.body.appendChild(iframe);

        // If 1.5 seconds pass without leaving the page, Google Maps app is not installed
        // Fallback to Apple Maps
        setTimeout(() => {
            const elapsed = Date.now() - now;
            if (elapsed < 1500) {
                window.location.href = appleURL;
            }
            if (document.body.contains(iframe)) {
                document.body.removeChild(iframe);
            }
        }, 1200);
    } else if (/android/.test(ua)) {
        // Android: Use geo:// protocol to open default maps app
        window.location.href = `geo:${lat},${lng}?q=${encodedName}`;
    } else {
        // Desktop/Other: Open Google Maps web version
        window.location.href = googleWebURL;
    }
}

// Make function globally accessible
declare global {
    interface Window {
        openSmartMap: (lat: number, lng: number, name?: string) => void;
    }
}

window.openSmartMap = openSmartMap;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize map links with data attributes
    const mapLinks = document.querySelectorAll<HTMLAnchorElement>('[data-map-lat][data-map-lng]');
    mapLinks.forEach((link) => {
        const lat = parseFloat(link.getAttribute('data-map-lat') || '0');
        const lng = parseFloat(link.getAttribute('data-map-lng') || '0');
        const name = link.getAttribute('data-map-name') || '';

        if (lat && lng) {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                openSmartMap(lat, lng, name);
            });
        }
    });
});

// Export for module usage
export default openSmartMap;
