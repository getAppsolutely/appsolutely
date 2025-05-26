export interface PageBuilderComponent {
  id: string;
  type: string;
  config: Record<string, any>;
  children?: PageBuilderComponent[];
}

export interface Container {
  id: string;
  components: PageBuilderComponent[];
  layout: 'vertical' | 'horizontal';
  columns?: number;
}

export interface ComponentDefinition {
  id: string;
  name: string;
  icon: string;
  preview: string;
  config_schema: Record<string, ConfigField>;
}

export interface ConfigField {
  type: 'text' | 'textarea' | 'number' | 'color' | 'select' | 'spacing' | 'boolean';
  default: any;
  options?: string[];
  min?: number;
  max?: number;
}

export interface ComponentCategory {
  id: string;
  name: string;
  components: ComponentDefinition[];
}

export interface PageData {
  id: number;
  title: string;
  slug: string;
  components: Container[];
  settings: Record<string, any>;
}

export interface PageBuilderState {
  page: PageData | null;
  selectedComponent: PageBuilderComponent | null;
  isDragging: boolean;
  history: PageBuilderComponent[][];
  historyIndex: number;
  componentRegistry: ComponentCategory[];
  isLoading: boolean;
}

export interface DragItem {
  type: 'component' | 'existing';
  componentType?: string;
  componentId?: string;
  sourceContainerId?: string;
}
