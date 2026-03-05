/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 */

import { observeInView } from '../utils/scroll-animation';

export function init(): void {
    observeInView('.hero-banner', { threshold: 0.3 });
}
