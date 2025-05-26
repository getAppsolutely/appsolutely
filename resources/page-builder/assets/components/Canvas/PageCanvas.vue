<template>
  <div class="h-full bg-gray-100 p-4 overflow-y-auto">
    <!-- Canvas Header -->
    <div class="mb-4 text-center">
      <h3 class="text-lg font-medium text-gray-900">Canvas</h3>
      <p class="text-sm text-gray-500">Drop components here to build your page</p>
    </div>

    <!-- Canvas Content -->
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm min-h-96">
      <!-- Containers -->
      <div v-if="page?.components.length" class="p-4">
        <div
          v-for="container in page.components"
          :key="container.id"
          class="mb-4 border-2 border-dashed border-gray-200 rounded-lg p-4 min-h-24"
          @dragover.prevent
          @drop="handleDrop($event, container.id)"
        >
          <!-- Container Header -->
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500">Container ({{ container.layout }})</span>
            <span class="text-xs text-gray-400">{{ container.components.length }} components</span>
          </div>

          <!-- Components in Container -->
          <div v-if="container.components.length" class="space-y-2">
            <div
              v-for="component in container.components"
              :key="component.id"
              class="p-3 bg-blue-50 border border-blue-200 rounded cursor-pointer hover:bg-blue-100"
              @click="selectComponent(component)"
            >
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-900">{{ component.type }}</span>
                <button
                  @click.stop="removeComponent(container.id, component.id)"
                  class="text-red-500 hover:text-red-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
              <div class="text-xs text-blue-700 mt-1">ID: {{ component.id }}</div>
            </div>
          </div>

          <!-- Empty Container Message -->
          <div v-else class="text-center py-8 text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <p class="text-sm">Drop components here</p>
          </div>
        </div>
      </div>

      <!-- Empty Canvas -->
      <div v-else class="p-8 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <p>No containers yet. Components will create containers automatically.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePageBuilderStore } from '../../stores/pageBuilder';
import type { PageBuilderComponent } from '../../types';

const store = usePageBuilderStore();

// Computed properties
const page = computed(() => store.page);

// Component selection
const selectComponent = (component: PageBuilderComponent) => {
  store.selectComponent(component);
};

// Remove component
const removeComponent = (containerId: string, componentId: string) => {
  store.removeComponent(containerId, componentId);
};

// Drop handler
const handleDrop = (event: DragEvent, containerId: string) => {
  event.preventDefault();

  if (!event.dataTransfer) return;

  try {
    const dragData = JSON.parse(event.dataTransfer.getData('application/json'));

    if (dragData.type === 'component') {
      store.addComponent(containerId, dragData.componentType);
    }
  } catch (error) {
    console.error('Error handling drop:', error);
  } finally {
    store.setDragging(false);
  }
};
</script>
