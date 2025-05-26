<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Components</h2>
      <p class="text-sm text-gray-500 mt-1">Drag components to the canvas</p>
    </div>

    <!-- Component Categories -->
    <div class="flex-1 overflow-y-auto">
      <div v-for="category in componentRegistry" :key="category.id" class="border-b border-gray-100">
        <!-- Category Header -->
        <div class="p-4 bg-gray-50">
          <h3 class="text-sm font-medium text-gray-900">{{ category.name }}</h3>
        </div>

        <!-- Components in Category -->
        <div class="p-2">
          <div
            v-for="component in category.components"
            :key="component.id"
            class="p-3 mb-2 border border-gray-200 rounded-lg cursor-move hover:border-blue-300 hover:bg-blue-50 transition-colors"
            draggable="true"
            @dragstart="handleDragStart($event, component)"
          >
            <div class="flex items-center space-x-3">
              <!-- Icon placeholder -->
              <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </div>

              <!-- Component Info -->
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">{{ component.name }}</div>
                <div class="text-xs text-gray-500">{{ component.id }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePageBuilderStore } from '../../stores/pageBuilder';
import type { ComponentDefinition } from '../../types';

const store = usePageBuilderStore();

// Computed properties
const componentRegistry = computed(() => store.componentRegistry);

// Drag and drop handlers
const handleDragStart = (event: DragEvent, component: ComponentDefinition) => {
  if (!event.dataTransfer) return;

  store.setDragging(true);

  // Set drag data
  event.dataTransfer.setData('application/json', JSON.stringify({
    type: 'component',
    componentType: component.id,
  }));

  event.dataTransfer.effectAllowed = 'copy';
};
</script>
