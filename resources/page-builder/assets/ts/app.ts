// Page Builder Main TypeScript Entry Point
import './services/PageBuilderService';
import './components/EditorManager';
import './components/BlockManager';
import './components/ConfigManager';
import './components/NotificationManager';
import './components/DeviceManager';
import './components/PreviewManager';

// Initialize Page Builder when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
  // Initialize with data from window object
  if (window.pageBuilderData && window.pageBuilderConfig) {
    const service = (window as any).pageBuilderService;
    if (service) {
      // renderPageData handles loading block registry first
      await service.renderPageData(window.pageBuilderData);
    }
  }
});
