/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 *
 * Observes only inner slides (.hero-banner__wrapper > .hero-banner), not the outer
 * section, so each slide fades in/out independently when scrolling.
 */

import { observeInView } from '../utils/scroll-animation';

export function init(): void {
    observeInView('.hero-banner__wrapper > .hero-banner', { threshold: 0.3 });
}
