<template>
  <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
    <!-- Left side - Page info -->
    <div class="flex items-center space-x-4">
      <h1 class="text-lg font-semibold text-gray-900">
        {{ page?.title || 'Page Builder' }}
      </h1>
      <span class="text-sm text-gray-500">
        {{ page?.slug }}
      </span>
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

// Save page function (placeholder)
const savePage = async () => {
  console.log('Save page functionality will be implemented');
  // TODO: Implement save functionality
};
</script>
