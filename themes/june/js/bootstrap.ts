/**
 * Bootstrap Configuration
 *
 * This file configures Bootstrap 5.3.0 and other JavaScript dependencies
 */

// Import Lodash for utility functions
import _ from 'lodash';
window._ = _ as any;

// Import asset URL utility (makes it globally available)
import './utils/asset-url';

/**
 * Bootstrap 5.3.0 JavaScript
 *
 * Bootstrap 5 is built on vanilla JavaScript and no longer requires jQuery.
 * All Bootstrap components are available through the bootstrap namespace.
 */
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally for debugging and manual initialization
window.bootstrap = bootstrap;

/**
 * Axios HTTP Client
 *
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import axios from 'axios';
window.axios = axios;

// Set default headers for AJAX requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF Token Configuration
 *
 * Laravel automatically generates a CSRF "token" for each active user session
 * managed by the application. This token is used to verify that the authenticated
 * user is the one actually making the requests to the application.
 */
const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo Configuration (Commented out)
 *
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

/**
 * Bootstrap Component Initialization
 *
 * Initialize Bootstrap components that require JavaScript.
 * Bootstrap 5 components are auto-initialized, but you can manually
 * initialize them here if needed.
 */
document.addEventListener('DOMContentLoaded', (): void => {
    // Initialize tooltips
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map((tooltipTriggerEl: Element) => {
        return new bootstrap.Tooltip(tooltipTriggerEl as HTMLElement);
    });

    // Initialize popovers
    const popoverTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map((popoverTriggerEl: Element) => {
        return new bootstrap.Popover(popoverTriggerEl as HTMLElement);
    });

    // Initialize toasts
    const toastElList = Array.from(document.querySelectorAll('.toast'));
    toastElList.map((toastEl: Element) => {
        return new bootstrap.Toast(toastEl as HTMLElement);
    });
});
