import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type {
  PageBuilderState,
  PageData,
  PageBuilderComponent,
  ComponentCategory,
  Container
} from '../types';

export type PreviewMode = 'mobile' | 'tablet' | 'desktop';

export const usePageBuilderStore = defineStore('pageBuilder', () => {
  // State
  const page = ref<PageData | null>(null);
  const selectedComponent = ref<PageBuilderComponent | null>(null);
  const isDragging = ref(false);
  const history = ref<Container[][]>([]);
  const historyIndex = ref(-1);
  const componentRegistry = ref<ComponentCategory[]>([]);
  const isLoading = ref(false);
  const isSaving = ref(false);
  const previewMode = ref<PreviewMode>('desktop');
  const componentSelected = ref(false);
  const draggingComponentType = ref<string | null>(null);

  // Getters
  const canUndo = computed(() => historyIndex.value > 0);
  const canRedo = computed(() => historyIndex.value < history.value.length - 1);

  // Preview mode dimensions
  const previewDimensions = computed(() => {
    switch (previewMode.value) {
      case 'mobile':
        return { width: 375, height: 812, label: 'Phone' };
      case 'tablet':
        return { width: 768, height: 1024, label: 'Tablet' };
      case 'desktop':
      default:
        return { width: '100%', height: '100%', label: 'Desktop' };
    }
  });

  // Actions
  const setPage = (pageData: PageData) => {
    page.value = {
      ...pageData,
      components: pageData.components || [], // Ensure components is always an array, defaults to [] if null/undefined
    };
    // Initialize history with current state
    // If pageData.components was null/undefined, page.value.components is now [], which is fine for history.
    history.value = [JSON.parse(JSON.stringify(page.value.components))];
    historyIndex.value = 0;
  };

  const setComponentRegistry = (registry: ComponentCategory[]) => {
    componentRegistry.value = registry;
  };

  const selectComponent = (component: PageBuilderComponent | null) => {
    selectedComponent.value = component;
    // Automatically show config panel when a component is selected
    if (component) {
      componentSelected.value = true;
    }
  };

  const setDragging = (dragging: boolean) => {
    isDragging.value = dragging;
  };

  const setPreviewMode = (mode: PreviewMode) => {
    previewMode.value = mode;
  };

  const setComponentSelected = (selected: boolean) => {
    componentSelected.value = selected;
  };

  const toggleConfigPanel = () => {
    componentSelected.value = !componentSelected.value;
  };

  const closeConfigPanel = () => {
    componentSelected.value = false;
    // Don't clear the selected component, just hide the panel
  };

  const setDraggingComponentType = (type: string | null) => {
    draggingComponentType.value = type;
  };

  const addToHistory = (containers: Container[]) => {
    // Remove any future history if we're not at the end
    if (historyIndex.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyIndex.value + 1);
    }

    // Add new state
    history.value.push(JSON.parse(JSON.stringify(containers)));
    historyIndex.value = history.value.length - 1;

    // Limit history size
    if (history.value.length > 50) {
      history.value.shift();
      historyIndex.value--;
    }
  };

  const undo = () => {
    if (canUndo.value && page.value) {
      historyIndex.value--;
      page.value.components = JSON.parse(JSON.stringify(history.value[historyIndex.value]));
    }
  };

  const redo = () => {
    if (canRedo.value && page.value) {
      historyIndex.value++;
      page.value.components = JSON.parse(JSON.stringify(history.value[historyIndex.value]));
    }
  };

  const addComponent = (containerId: string, componentType: string, index?: number) => {
    if (!page.value) return;

    const isLayoutComponentType = componentType === 'vertical' || componentType === 'horizontal';

    if (containerId === 'container_default' && isLayoutComponentType) {
      // Adding a new LAYOUT to an empty page or to the general "add layout" area
      const newLayoutContainer: Container = {
        id: `component_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
        layout: componentType as ('vertical' | 'horizontal'),
        components: [],
      };

      // Simply add the new layout to the page's components array.
      // If an index is provided (e.g., for dropping between existing layouts), use it.
      if (index !== undefined && index < page.value.components.length) {
        page.value.components.splice(index, 0, newLayoutContainer);
      } else {
        page.value.components.push(newLayoutContainer);
      }
      addToHistory(page.value.components);

    } else if (containerId !== 'container_default' && !isLayoutComponentType) {
      // Adding a BLOCK to an EXISTING LAYOUT CONTAINER
      const targetLayoutContainer = page.value.components.find(c => c.id === containerId);

      if (targetLayoutContainer) {
        const newBlockComponent: PageBuilderComponent = {
          id: `component_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
          type: componentType,
          config: getDefaultConfig(componentType),
        };
        // If an index is provided for inserting the block, use it.
        if (index !== undefined && index < targetLayoutContainer.components.length) {
          targetLayoutContainer.components.splice(index, 0, newBlockComponent);
        } else {
          targetLayoutContainer.components.push(newBlockComponent);
        }
        addToHistory(page.value.components);
      } else {
        console.warn('Target layout container for block not found:', containerId);
      }
    } else {
      // This case should ideally be prevented by drag-and-drop rules UI
      // e.g., trying to drag a layout into another layout, or a block directly to body.
      console.warn(`Invalid component drop: type '${componentType}' into container '${containerId}'`);
    }
  };

  const removeComponent = (containerId: string, componentId: string) => {
    if (!page.value) return;

    const container = page.value.components.find(c => c.id === containerId);
    if (container) {
      const index = container.components.findIndex(c => c.id === componentId);
      if (index > -1) {
        container.components.splice(index, 1);
        addToHistory(page.value.components);

        // Clear selection if removed component was selected
        if (selectedComponent.value?.id === componentId) {
          selectedComponent.value = null;
        }
      }
    }
  };

  const updateComponentConfig = (componentId: string, config: Record<string, any>) => {
    if (!page.value) return;

    // Find component in all containers
    for (const container of page.value.components) {
      const component = container.components.find(c => c.id === componentId);
      if (component) {
        component.config = { ...component.config, ...config };
        addToHistory(page.value.components);
        break;
      }
    }
  };

  const getDefaultConfig = (componentType: string): Record<string, any> => {
    for (const category of componentRegistry.value) {
      const componentDef = category.components.find(c => c.id === componentType);
      if (componentDef) {
        const config: Record<string, any> = {};
        Object.entries(componentDef.config_schema).forEach(([key, field]) => {
          config[key] = field.default;
        });
        return config;
      }
    }
    return {};
  };

  // Save page data to API
  const savePageData = async (pageId: number, data: any) => {
    const apiBase = document.getElementById('page-builder-app')?.dataset.apiBase;
    if (!apiBase) return;
    const response = await fetch(`${apiBase}/pages/${pageId}/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
      },
      body: JSON.stringify(data),
    });
    return response.json();
  };

  return {
    // State
    page,
    selectedComponent,
    isDragging,
    history,
    historyIndex,
    componentRegistry,
    isLoading,
    isSaving,
    previewMode,
    componentSelected,
    draggingComponentType,

    // Getters
    canUndo,
    canRedo,
    previewDimensions,

    // Actions
    setPage,
    setComponentRegistry,
    selectComponent,
    setDragging,
    setPreviewMode,
    setComponentSelected,
    toggleConfigPanel,
    closeConfigPanel,
    setDraggingComponentType,
    addToHistory,
    undo,
    redo,
    addComponent,
    removeComponent,
    updateComponentConfig,
    getDefaultConfig,
    savePageData,
  };
});
