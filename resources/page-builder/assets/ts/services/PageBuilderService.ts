// Page Builder Service - Core functionality
import type { Editor } from 'types/grapesjs';
import type { BlockRegistryCategory, BlockDefinition, PreviewContent } from 'types/pagebuilder';

export class PageBuilderService {
    private editor: Editor | null = null;
    private blockRegistry: BlockRegistryCategory[] = [];
    private initializationPromise: Promise<void>;

    constructor() {
        this.initializationPromise = this.initializeEditor();
    }

    private async initializeEditor(): Promise<void> {
        // Wait for GrapesJS to be available
        if (typeof window !== 'undefined' && (window as any).grapesjs) {
            this.setupEditor();
        } else {
            // Retry after a short delay
            await new Promise((resolve) => setTimeout(resolve, 100));
            return this.initializeEditor();
        }
    }

    private setupEditor(): void {
        const grapesjs = window.grapesjs;

        if (!grapesjs) {
            console.error('GrapesJS is not available');
            return;
        }

        this.editor = grapesjs.init({
            container: '#editor-canvas',
            fromElement: false,
            height: '100%',
            width: 'auto',
            storageManager: false,
            canvas: {
                styles: [this.getThemedStyles()],
            },
            blockManager: {
                appendTo: '#blocks',
            },
            panels: {
                defaults: [
                    {
                        id: 'blocks',
                        el: '#blocks',
                    },
                ],
            },
            deviceManager: {
                devices: [
                    {
                        id: 'desktop',
                        name: 'Desktop',
                        width: '',
                    },
                    {
                        id: 'tablet',
                        name: 'Tablet',
                        width: '768px',
                        widthMedia: '992px',
                    },
                    {
                        id: 'mobile',
                        name: 'Mobile',
                        width: '320px',
                        widthMedia: '768px',
                    },
                ],
            },
        });

        this.setupEventListeners();
    }

    private setupEventListeners(): void {
        if (!this.editor) return;

        this.editor.on('load', () => {
            if (!this.editor) return;

            this.editor.on('component:remove', () => this.updateBlockCount());
            this.editor.on('component:add', (component: unknown) => {
                this.ensureComponentReference(component);
                this.updateBlockCount();
            });
            this.editor.on('component:update', (component: unknown) => {
                this.ensureComponentReference(component);
            });
        });
    }

    private getThemedStyles(): string {
        // Use themed styles from window configuration (set by Blade template)
        return window.pageBuilderConfig?.themedStyles || '';
    }

    public async loadBlockRegistry(): Promise<void> {
        try {
            // Use the URL from window configuration (set by Blade template)
            const blockRegistryUrl = window.pageBuilderConfig?.blockRegistryUrl || '/admin/api/pages/block-registry';

            const response = await fetch(blockRegistryUrl);
            const result = (await response.json()) as { data: BlockRegistryCategory[] };
            this.blockRegistry = result.data;

            this.registerBlocks();
        } catch (error) {
            console.error('Failed to load block registry:', error);
        }
    }

    private registerBlocks(): void {
        if (!this.editor) return;

        const blockManager = this.editor.BlockManager;
        const domComponents = this.editor.DomComponents;

        this.blockRegistry.forEach((category) => {
            const categoryId = category.title;
            const categoryLabel = category.label || category.title;

            category.blocks
                .sort((a: BlockDefinition, b: BlockDefinition) => (a.sort || 0) - (b.sort || 0))
                .forEach((comp: BlockDefinition) => {
                    const {
                        id,
                        label,
                        type,
                        content = '<div></div>',
                        tagName = 'div',
                        description = '',
                        sort = 0,
                        droppable = false,
                    } = comp;

                    domComponents.addType(type, {
                        model: {
                            defaults: {
                                tagName,
                                content,
                            },
                        },
                    });

                    blockManager.add(type, {
                        label: this.createBlockLabel(label, description),
                        category: {
                            id: categoryId,
                            label: categoryLabel,
                        },
                        content: {
                            block_id: id,
                            type,
                            droppable,
                        },
                        order: sort,
                    });
                });
        });
    }

    private createBlockLabel(label: string, description: string): string {
        return `
      <div class="flex items-start text-left">
        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mr-3"></div>
        <div class="flex-1">
          <strong class="text-base">${label}</strong>
          <div class="text-sm text-gray-500">${description}</div>
        </div>
      </div>
    `;
    }

    public async renderPageData(pageData: string | null): Promise<void> {
        // Wait for editor initialization to complete
        await this.initializationPromise;

        if (!this.editor) {
            console.error('Editor not initialized yet');
            return;
        }

        // Ensure block registry is loaded first
        if (this.blockRegistry.length === 0) {
            await this.loadBlockRegistry();
        }

        if (pageData) {
            try {
                const parsedData = JSON.parse(pageData);
                this.editor.loadProjectData(parsedData);
                this.updateBlockCount();
            } catch (error) {
                console.error('Failed to parse page data:', error);
            }
        } else {
            const defaultHtml = `
        <h1 class="text-3xl mt-5 text-center">Hello, welcome to Page Builder<br/>Start dragging components from the right!</h1>
      `;
            this.editor.addComponents(defaultHtml);
        }
    }

    public async savePageData(): Promise<Response> {
        // Wait for editor initialization to complete
        await this.initializationPromise;

        if (!this.editor) {
            throw new Error('Editor not initialized');
        }

        const projectData = this.editor.getProjectData();

        // Ensure all components have unique references
        const components = this.editor.getComponents();
        components.forEach((component: unknown) => {
            this.ensureComponentReference(component);
        });

        // Use the URL from window configuration (set by Blade template)
        const saveUrl = window.pageBuilderConfig?.saveUrl || '/admin/api/pages/save';

        return fetch(saveUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({
                data: projectData,
            }),
        });
    }

    public async resetPageData(): Promise<Response> {
        // Wait for editor initialization to complete
        await this.initializationPromise;

        if (!this.editor) {
            throw new Error('Editor not initialized');
        }

        this.editor.DomComponents.clear();
        const defaultHtml = `
      <h1 class="text-3xl mt-5 text-center">Hello, welcome to Page Builder<br/>Start dragging components from the right!</h1>
    `;
        this.editor.addComponents(defaultHtml);

        // Use the URL from window configuration (set by Blade template)
        const resetUrl = window.pageBuilderConfig?.resetUrl || '/admin/api/pages/reset';

        return fetch(resetUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({}),
        });
    }

    public async getPreviewContent(): Promise<PreviewContent> {
        // Wait for editor initialization to complete
        await this.initializationPromise;

        if (!this.editor) {
            throw new Error('Editor not initialized');
        }

        return {
            html: this.editor.getHtml(),
            css: this.editor.getCss(),
        };
    }

    private generateRandomId(type: string): string {
        const rand = crypto.getRandomValues(new Uint32Array(1))[0].toString(36);
        return `${type.toLowerCase()}-${rand}`;
    }

    private ensureComponentReference(component: unknown): void {
        // Type guard for Component interface
        if (component && typeof component === 'object' && 'get' in component && 'set' in component) {
            const comp = component as { get: (key: string) => unknown; set: (key: string, value: unknown) => void };
            if (!comp.get('reference')) {
                const type = comp.get('type');
                comp.set('reference', this.generateRandomId(typeof type === 'string' ? type : 'component'));
            }
        }
    }

    private updateBlockCount(): void {
        if (!this.editor) return;

        const wrapper = this.editor.getWrapper();
        if (!wrapper || typeof wrapper.components !== 'function') {
            return;
        }
        const components = wrapper.components();
        const countElement = document.getElementById('block-count');
        if (countElement) {
            countElement.textContent = components.length.toString();
        }
    }

    private getCSRFToken(): string {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') || '' : '';
    }

    public async undo(): Promise<void> {
        // Wait for editor initialization to complete
        await this.initializationPromise;
        if (!this.editor) {
            throw new Error('Editor not initialized');
        }
        this.editor.UndoManager.undo();
    }

    public async redo(): Promise<void> {
        // Wait for editor initialization to complete
        await this.initializationPromise;
        if (!this.editor) {
            throw new Error('Editor not initialized');
        }
        this.editor.UndoManager.redo();
    }

    public async setDevice(device: string): Promise<void> {
        // Wait for editor initialization to complete
        await this.initializationPromise;
        if (!this.editor) {
            throw new Error('Editor not initialized');
        }
        this.editor.setDevice(device);
    }
}

// Export singleton instance
export const pageBuilderService = new PageBuilderService();

// Make it available globally
if (typeof window !== 'undefined') {
    window.pageBuilderService = pageBuilderService;
}
