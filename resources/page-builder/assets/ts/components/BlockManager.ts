// Block Manager - Handles block-related functionality
import { pageBuilderService } from '../services/PageBuilderService';
import type { BlockRegistryCategory, BlockDefinition } from '../../../../types/pagebuilder';

export class BlockManager {
    private service: typeof pageBuilderService;

    constructor() {
        this.service = pageBuilderService;
        this.initializeBlockManagement();
    }

    private initializeBlockManagement(): void {
        // Initialize block registry loading
        this.loadBlockRegistry();
    }

    private async loadBlockRegistry(): Promise<void> {
        try {
            await this.service.loadBlockRegistry();
        } catch (error) {
            console.error('Failed to load block registry:', error);
        }
    }

    public async registerCustomBlock(_blockConfig: BlockDefinition): Promise<void> {
        // Method to register custom blocks dynamically
        // This would be used for theme-specific blocks
        // TODO: Implement custom block registration logic
    }

    public getBlockCategories(): BlockRegistryCategory[] {
        // Access private blockRegistry through service
        // Note: This is a workaround - ideally blockRegistry should be public or have a getter
        return (this.service as unknown as { blockRegistry: BlockRegistryCategory[] }).blockRegistry || [];
    }

    public getBlocksByCategory(categoryId: string): BlockDefinition[] {
        const category = this.getBlockCategories().find((cat) => cat.title === categoryId);
        return category ? category.blocks : [];
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new BlockManager();
});
