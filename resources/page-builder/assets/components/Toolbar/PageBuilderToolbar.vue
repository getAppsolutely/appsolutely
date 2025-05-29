<template>
  <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between fixed top-0 left-0 right-0 z-40 shadow-sm h-16">
    <!-- Left side - Page info -->
    <div class="flex items-center space-x-4 min-w-0">
      <h1 class="text-lg font-semibold text-gray-900 truncate">
        {{ page?.title || 'Page Builder' }}
      </h1>
      <span class="text-sm text-gray-500 truncate">
        {{ page?.slug }}
      </span>
    </div>

    <!-- Center - Preview Buttons -->
    <div class="flex items-center justify-center flex-1">
      <div class="flex items-center space-x-2">
        <!-- Preview mode indicator -->
        <span class="text-xs text-gray-500 mr-2">{{ previewDimensions.label }}</span>

        <button
          :class="previewMode === 'mobile' ? activePreviewClass : previewClass"
          @click="setPreviewMode('mobile')"
          title="Mobile Preview (375px)"
        >
          <!-- Mobile SVG (24x24) -->
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="7" y="3" width="10" height="18" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="17" r="1.5" />
          </svg>
        </button>
        <button
          :class="previewMode === 'tablet' ? activePreviewClass : previewClass"
          @click="setPreviewMode('tablet')"
          title="Tablet Preview (768px)"
        >
          <!-- Tablet SVG (24x24) -->
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="4" y="5" width="16" height="14" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="17" r="1.5" />
          </svg>
        </button>
        <button
          :class="previewMode === 'desktop' ? activePreviewClass : previewClass"
          @click="setPreviewMode('desktop')"
          title="Desktop Preview (100%)"
        >
          <!-- Desktop SVG (24x24) -->
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="3" y="7" width="18" height="10" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="8" y="17" width="8" height="2" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Right side - Actions -->
    <div class="flex items-center space-x-3">
      <!-- Undo/Redo -->
      <div class="flex items-center space-x-1">
        <button
          :disabled="!canUndo"
          @click="store.undo()"
          class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
          title="Undo"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
          </svg>
        </button>
        <button
          :disabled="!canRedo"
          @click="store.redo()"
          class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
          title="Redo"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6-6m6 6l-6 6"/>
          </svg>
        </button>
      </div>

      <!-- Save -->
      <button
        @click="savePage"
        :disabled="isSaving"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ isSaving ? 'Saving...' : 'Save' }}
      </button>

      <!-- Preview -->
      <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
        Preview
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePageBuilderStore } from '../../stores/pageBuilder';

const store = usePageBuilderStore();

// Computed properties
const page = computed(() => store.page);
const canUndo = computed(() => store.canUndo);
const canRedo = computed(() => store.canRedo);
const isSaving = computed(() => store.isSaving);
const previewMode = computed(() => store.previewMode);
const previewDimensions = computed(() => store.previewDimensions);

// Actions
const setPreviewMode = (mode: 'mobile' | 'tablet' | 'desktop') => {
  store.setPreviewMode(mode);
};

const previewClass = 'p-2 rounded-full border border-gray-300 text-gray-500 bg-white hover:bg-gray-100';
const activePreviewClass = 'p-2 rounded-full border-2 border-blue-500 text-blue-600 bg-blue-50 shadow';

// Save page function (placeholder)
const savePage = async () => {
  console.log('Save page functionality will be implemented');
  // TODO: Implement save functionality
};
</script>

<style scoped>
.bg-white.fixed {
  left: 0;
  right: 0;
  top: 0;
  z-index: 40;
}
</style>
