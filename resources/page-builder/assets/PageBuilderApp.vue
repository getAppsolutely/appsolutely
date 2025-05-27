<template>
  <div class="page-builder h-screen flex flex-col bg-gray-50">
    <!-- Toolbar -->
    <PageBuilderToolbar />

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden mt-16">
      <!-- Canvas Area -->
      <div class="flex-1 flex flex-col">
        <PageCanvas class="flex-1" />
      </div>

      <!-- Component Sidebar (right) -->
      <ComponentSidebar class="w-80 border-l border-gray-200 bg-white" />

      <!-- Configuration Panel -->
      <ConfigPanel
        v-if="selectedComponent"
        class="w-80 border-l border-gray-200 bg-white"
      />
    </div>

    <!-- Loading Overlay -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { usePageBuilderStore } from './stores/pageBuilder';
import PageBuilderToolbar from './components/Toolbar/PageBuilderToolbar.vue';
import ComponentSidebar from './components/Sidebar/ComponentSidebar.vue';
import PageCanvas from './components/Canvas/PageCanvas.vue';
import ConfigPanel from './components/ConfigPanel/ConfigPanel.vue';

const store = usePageBuilderStore();

// Computed properties
const isLoading = computed(() => store.isLoading);
const selectedComponent = computed(() => store.selectedComponent);

// Get page data from DOM attributes
const getPageData = () => {
  const app = document.getElementById('page-builder-app');
  if (!app) return null;

  return {
    pageId: app.dataset.pageId,
    pageTitle: app.dataset.pageTitle,
    apiBase: app.dataset.apiBase,
  };
};

// Load initial data
const loadInitialData = async () => {
  const pageData = getPageData();
  if (!pageData) return;

  store.isLoading = true;

  try {
    // Load component registry
    const registryResponse = await fetch(`${pageData.apiBase}/pages/components/registry`);
    const registry = await registryResponse.json();
    // Support both {categories: ...} and {data: {categories: ...}}
    const categories = registry.categories || (registry.data && registry.data.categories) || [];
    store.setComponentRegistry(categories);

    // Load page data
    const pageResponse = await fetch(`${pageData.apiBase}/pages/${pageData.pageId}/data`);
    const pageInfo = (await pageResponse.json()).data;

    // Initialize page with default container if no components exist
    const components = pageInfo.components.length > 0 ? pageInfo.components : [
      {
        id: 'container_default',
        components: [],
        layout: 'vertical' as const,
      }
    ];

    store.setPage({
      id: parseInt(pageData.pageId!),
      title: pageData.pageTitle!,
      slug: pageInfo.page.slug,
      components,
      settings: pageInfo.page.builder_settings || {},
    });

  } catch (error) {
    console.error('Failed to load page builder data:', error);
  } finally {
    store.isLoading = false;
  }
};

onMounted(() => {
  loadInitialData();
});
</script>

<style scoped>
.page-builder {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
