<template>
  <div class="h-full bg-gray-100 p-4 overflow-y-auto">
    <!-- Canvas Header -->
    <div class="mb-4 text-center">
      <h3 class="text-lg font-medium text-gray-900">Canvas</h3>
      <p class="text-sm text-gray-500">Drop components here to build your page</p>
      <div class="text-xs text-gray-400 mt-1">
        Preview: {{ previewDimensions.label }}
        <span v-if="previewMode !== 'desktop'">
          ({{ previewDimensions.width }}px × {{ previewDimensions.height }}px)
        </span>
      </div>
    </div>

    <!-- Canvas Content -->
    <div class="flex justify-center">
      <!-- Device Frame -->
      <div
        :class="deviceFrameClass"
        :style="deviceFrameStyle"
        class="bg-white rounded-lg shadow-lg min-h-96 transition-all duration-300"
      >
        <!-- Screen Content -->
        <div
          class="h-full overflow-y-auto"
          :class="previewMode !== 'desktop' ? 'p-4' : 'px-4 pt-4 pb-32'"
        >
          <!-- Rendered Layouts (if any) -->
          <div v-if="page?.components.length">
            <div
              v-for="(container, index) in page.components"
              :key="container.id"
              class="mb-4 border-2 border-dashed border-gray-200 rounded-lg p-4 min-h-24 relative"
              :class="containerResponsiveClass"
              @dragover="handleDragOver($event, container)"
              @drop="handleDrop($event, container.id, index)"
            >
              <!-- Container Header -->
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <div class="text-gray-500" :title="container.layout === 'vertical' ? 'Vertical Layout' : 'Horizontal Layout'">
                    <svg v-if="container.layout === 'vertical'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <rect x="4" y="2" width="16" height="4" rx="1"/>
                      <rect x="4" y="10" width="16" height="4" rx="1"/>
                      <rect x="4" y="18" width="16" height="4" rx="1"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <rect x="2" y="4" width="4" height="16" rx="1"/>
                      <rect x="10" y="4" width="4" height="16" rx="1"/>
                      <rect x="18" y="4" width="4" height="16" rx="1"/>
                    </svg>
                  </div>
                  <span class="text-xs text-gray-500">
                    {{ container.layout === 'vertical' ? 'Section' : 'Row' }}
                  </span>
                </div>
                <span class="text-xs text-gray-400">{{ container.components.length }} components</span>
              </div>

              <!-- Components in Container (Blocks) -->
              <div v-if="container.components.length" class="space-y-2">
                <div
                  v-for="(component, compIndex) in container.components"
                  :key="component.id"
                  class="p-3 bg-blue-50 border border-blue-200 rounded cursor-pointer hover:bg-blue-100 transition-colors"
                  :class="componentResponsiveClass"
                  @click="selectComponent(component)"
                  @dragover="handleBlockDragOver($event, container, compIndex)"
                  @drop.stop="handleBlockDrop($event, container.id, compIndex)"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-900">{{ component.type }}</span>
                    <div class="flex items-center gap-1">
                      <button
                        @click.stop="selectComponentAndShowConfig(component)"
                        class="p-1 rounded transition-colors"
                        :class="selectedComponent?.id === component.id && componentSelected
                          ? 'text-blue-600 bg-blue-100 hover:bg-blue-200'
                          : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50'"
                        title="Configure Component"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                      </button>
                      <button
                        @click.stop="removeComponent(container.id, component.id)"
                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                        title="Remove Component"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="text-xs text-blue-700 mt-1">ID: {{ component.id }}</div>
                </div>
              </div>

              <!-- Empty Layout Message (if a layout has no blocks) -->
              <div v-else class="text-center py-8 text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <p class="text-sm">
                  Drop block components here
                </p>
              </div>
            </div>

            <!-- Explicit Drop Zone for adding new layouts (only if layouts already exist) -->
            <div
              v-if="page.components.length > 0"
              class="my-4 min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 transition-colors"
              :class="{
                'border-blue-500 bg-blue-50 text-blue-500': store.isDragging && (draggingComponentType === 'vertical' || draggingComponentType === 'horizontal'),
                'hover:border-blue-500 hover:bg-blue-50 hover:text-blue-500': !store.isDragging
              }"
              @dragover="handleDragOver($event, null)"
              @drop="handleDrop($event, 'container_default', page.components.length)"
            >
              <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
              Add New Layout to Page
            </div>

          </div>

          <!-- Empty Canvas Message (if no layouts on page at all) -->
          <div v-else
            class="p-8 text-center text-gray-400 min-h-[200px] flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg transition-colors"
            :class="{
                'border-blue-500 bg-blue-50 text-blue-500': store.isDragging && (draggingComponentType === 'vertical' || draggingComponentType === 'horizontal'),
                'hover:border-blue-500 hover:bg-blue-50 hover:text-blue-500': !store.isDragging
            }"
            @dragover="handleDragOver($event, null)"
            @drop="handleDrop($event, 'container_default', 0)"
            >
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <p>Drop layout components here to get started</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePageBuilderStore } from '../../stores/pageBuilder';
import type { PageBuilderComponent, Container } from '../../types';

const store = usePageBuilderStore();

// Computed properties
const page = computed(() => store.page);
const previewMode = computed(() => store.previewMode);
const previewDimensions = computed(() => store.previewDimensions);
const componentSelected = computed(() => store.componentSelected);
const selectedComponent = computed(() => store.selectedComponent);
const draggingComponentType = computed(() => store.draggingComponentType);

// Device frame styling
const deviceFrameClass = computed(() => {
  switch (previewMode.value) {
    case 'mobile':
      return 'border-8 border-gray-800 rounded-[2.5rem] shadow-xl';
    case 'tablet':
      return 'border-4 border-gray-700 rounded-[1.5rem] shadow-xl';
    case 'desktop':
    default:
      return 'w-full max-w-none';
  }
});

const deviceFrameStyle = computed(() => {
  if (previewMode.value === 'desktop') {
    return {
      width: '100%',
      minHeight: '600px'
    };
  }

  return {
    width: `${previewDimensions.value.width}px`,
    height: `${previewDimensions.value.height}px`,
    maxWidth: '100%'
  };
});

// Responsive classes for components
const containerResponsiveClass = computed(() => {
  switch (previewMode.value) {
    case 'mobile':
      return 'text-sm';
    case 'tablet':
      return 'text-sm';
    case 'desktop':
    default:
      return '';
  }
});

const componentResponsiveClass = computed(() => {
  switch (previewMode.value) {
    case 'mobile':
      return 'text-xs';
    case 'tablet':
      return 'text-sm';
    case 'desktop':
    default:
      return '';
  }
});

// Component selection
const selectComponent = (component: PageBuilderComponent) => {
  store.selectComponent(component);
};

// Select component and show config panel
const selectComponentAndShowConfig = (component: PageBuilderComponent) => {
  // If the same component is already selected and config panel is visible, toggle it off
  if (selectedComponent.value?.id === component.id && componentSelected.value) {
    store.closeConfigPanel();
  } else {
    // Otherwise, select the component and show the config panel
    store.selectComponent(component);
  }
};

// Remove component
const removeComponent = (containerId: string, componentId: string) => {
  store.removeComponent(containerId, componentId);
};

// Drop handler
const handleDrop = (event: DragEvent, containerId: string, index?: number) => {
  event.preventDefault();
  event.stopPropagation(); // Prevent drop from bubbling to parent containers

  if (!event.dataTransfer) return;

  try {
    const dragData = JSON.parse(event.dataTransfer.getData('application/json'));

    if (dragData.type === 'component') {
      const componentType = dragData.componentType;
      const isLayoutComponent = componentType === 'vertical' || componentType === 'horizontal';

      // Rule: Body (container_default) only accepts Layout components.
      if (containerId === 'container_default' && !isLayoutComponent) {
        console.warn('Page canvas can only accept Layout components (Vertical/Horizontal) directly.');
        return;
      }

      // Rule: Layouts only accept Block components (not other Layouts).
      // This is checked by seeing if the target containerId is NOT container_default (meaning it's an existing layout)
      // AND the thing being dragged IS a layout component.
      if (containerId !== 'container_default' && isLayoutComponent) {
        console.warn('Layouts cannot be nested inside other layouts. Drop blocks here instead.');
        return;
      }

      // If we are here, the drop is valid according to basic rules
      store.addComponent(containerId, componentType, index);
    }
  } catch (error) {
    console.error('Error handling drop:', error);
  } finally {
    store.setDragging(false);
    store.setDraggingComponentType(null);
  }
};

// Drag over an existing Block (for reordering or dropping new block within same layout)
const handleBlockDragOver = (event: DragEvent, targetLayoutContainer: Container, targetBlockIndex: number) => {
  // TODO: Implement logic for highlighting drop position between blocks
  event.preventDefault();
  event.dataTransfer!.dropEffect = 'copy';
};

// Drop onto an existing Block (implies reordering or inserting at this position)
const handleBlockDrop = (event: DragEvent, targetLayoutId: string, targetBlockIndex: number) => {
  event.preventDefault();
  event.stopPropagation();
  if (!event.dataTransfer) return;
  try {
    const dragData = JSON.parse(event.dataTransfer.getData('application/json'));
    if (dragData.type === 'component') {
      const componentType = dragData.componentType;
      const isLayoutComponent = componentType === 'vertical' || componentType === 'horizontal';
      if (isLayoutComponent) {
        console.warn('Cannot drop a layout component inside another layout\'s block list.');
        return;
      }
      // Dropping a block into a layout at a specific index
      store.addComponent(targetLayoutId, componentType, targetBlockIndex);
    }
  } catch (error) {
    console.error('Error handling block drop:', error);
  } finally {
    store.setDragging(false);
    store.setDraggingComponentType(null);
  }
};

const handleDragOver = (event: DragEvent, targetContainer: Container | null) => {
  if (!event.dataTransfer) return;

  const isDraggingLayout = draggingComponentType.value === 'vertical' || draggingComponentType.value === 'horizontal';

  if (targetContainer) { // We are dragging over an existing layout item
    if (targetContainer.layout && isDraggingLayout) {
      event.dataTransfer.dropEffect = 'none';
    } else {
      event.preventDefault();
      event.dataTransfer.dropEffect = 'copy';
    }
  } else { // We are dragging over a BODY drop zone (targetContainer is null)
    if (isDraggingLayout) {
      event.preventDefault();
      event.dataTransfer.dropEffect = 'copy';
    } else {
      event.dataTransfer.dropEffect = 'none';
    }
  }
};
</script>

<style scoped>
/* Custom scrollbar for mobile/tablet preview */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
