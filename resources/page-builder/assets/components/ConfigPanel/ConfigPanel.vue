<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Configuration</h2>
      <p class="text-sm text-gray-500 mt-1">Edit component properties</p>
    </div>

    <!-- Component Info -->
    <div v-if="selectedComponent" class="p-4 border-b border-gray-100">
      <div class="text-sm font-medium text-gray-900">{{ selectedComponent.type }}</div>
      <div class="text-xs text-gray-500 mt-1">ID: {{ selectedComponent.id }}</div>
    </div>

    <!-- Configuration Form -->
    <div v-if="selectedComponent && componentSchema" class="flex-1 overflow-y-auto p-4">
      <div class="space-y-4">
        <div v-for="(field, key) in componentSchema" :key="key">
          <!-- Text Input -->
          <div v-if="field.type === 'text'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }}</label>
            <input
              type="text"
              :value="selectedComponent.config[key] || field.default"
              @input="updateConfig(key, ($event.target as HTMLInputElement).value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Textarea -->
          <div v-else-if="field.type === 'textarea'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }}</label>
            <textarea
              :value="selectedComponent.config[key] || field.default"
              @input="updateConfig(key, ($event.target as HTMLTextAreaElement).value)"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>

          <!-- Number Input -->
          <div v-else-if="field.type === 'number'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }}</label>
            <input
              type="number"
              :value="selectedComponent.config[key] || field.default"
              :min="field.min"
              :max="field.max"
              @input="updateConfig(key, parseInt(($event.target as HTMLInputElement).value))"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Select -->
          <div v-else-if="field.type === 'select'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }}</label>
            <select
              :value="selectedComponent.config[key] || field.default"
              @change="updateConfig(key, ($event.target as HTMLSelectElement).value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option v-for="option in field.options" :key="option" :value="option">
                {{ option }}
              </option>
            </select>
          </div>

          <!-- Color -->
          <div v-else-if="field.type === 'color'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }}</label>
            <input
              type="color"
              :value="selectedComponent.config[key] || field.default"
              @input="updateConfig(key, ($event.target as HTMLInputElement).value)"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Fallback for unknown types -->
          <div v-else class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">{{ key }} ({{ field.type }})</label>
            <input
              type="text"
              :value="selectedComponent.config[key] || field.default"
              @input="updateConfig(key, ($event.target as HTMLInputElement).value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- No Selection -->
    <div v-else class="flex-1 flex items-center justify-center text-gray-400">
      <div class="text-center">
        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p>Select a component to configure</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePageBuilderStore } from '../../stores/pageBuilder';

const store = usePageBuilderStore();

// Computed properties
const selectedComponent = computed(() => store.selectedComponent);

const componentSchema = computed(() => {
  if (!selectedComponent.value) return null;

  // Find the component definition in the registry
  for (const category of store.componentRegistry) {
    const componentDef = category.components.find(c => c.id === selectedComponent.value?.type);
    if (componentDef) {
      return componentDef.config_schema;
    }
  }
  return null;
});

// Update component configuration
const updateConfig = (key: string, value: any) => {
  if (!selectedComponent.value) return;

  store.updateComponentConfig(selectedComponent.value.id, {
    [key]: value
  });
};
</script>
