/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 */

import { observeInView } from '../utils/scroll-animation';

document.addEventListener('DOMContentLoaded', () => {
    observeInView('.hero-banner', { threshold: 0.3 });
});
