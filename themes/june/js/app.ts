/**
 * June Theme Entry Point
 *
 * Loads Bootstrap, Axios, Lodash, and theme components.
 * Uses vanilla JavaScript with Livewire for interactivity.
 * Component init is centralized in init.ts.
 */

import './bootstrap';
import './components/lazy-loading.ts';
import './assets';
import './init';

// Store locations exports openSmartMap (used by Blade)
import './components/store-locations';
