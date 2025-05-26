import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PageBuilderApp from './PageBuilderApp.vue';

// Create Vue app
const app = createApp(PageBuilderApp);

// Add Pinia for state management
const pinia = createPinia();
app.use(pinia);

// Mount the app
app.mount('#page-builder-app');
