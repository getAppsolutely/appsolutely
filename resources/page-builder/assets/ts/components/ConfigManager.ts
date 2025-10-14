// Config Manager - Handles configuration panel functionality
export class ConfigManager {
  private currentBlock: any = null;

  constructor() {
    this.initializeConfigPanel();
  }

  private initializeConfigPanel(): void {
    this.setupTabNavigation();
    this.setupConfigForm();
  }

  private setupTabNavigation(): void {
    const blocksTab = document.getElementById('blocks-tab');
    const configTab = document.getElementById('config-tab');
    const blocksContent = document.getElementById('blocks-content');
    const configContent = document.getElementById('config-content');

    if (blocksTab && configTab && blocksContent && configContent) {
      blocksTab.addEventListener('click', () => {
        this.switchToTab('blocks', blocksTab, configTab, blocksContent, configContent);
      });

      configTab.addEventListener('click', () => {
        this.switchToTab('config', blocksTab, configTab, blocksContent, configContent);
      });
    }
  }

  private switchToTab(
    activeTab: string,
    blocksTab: HTMLElement,
    configTab: HTMLElement,
    blocksContent: HTMLElement,
    configContent: HTMLElement
  ): void {
    if (activeTab === 'blocks') {
      blocksTab.classList.add('active');
      configTab.classList.remove('active');
      blocksContent.classList.remove('hidden');
      configContent.classList.add('hidden');
    } else {
      configTab.classList.add('active');
      blocksTab.classList.remove('active');
      configContent.classList.remove('hidden');
      blocksContent.classList.add('hidden');
    }
  }

  private setupConfigForm(): void {
    // Initialize empty config form
    this.showEmptyConfig();
  }

  public showBlockConfig(block: any): void {
    this.currentBlock = block;
    this.renderConfigForm(block);
    this.switchToConfigTab();
  }

  private renderConfigForm(block: any): void {
    const configForm = document.getElementById('config-form');
    if (!configForm) return;

    // Clear existing form
    configForm.innerHTML = '';

    // Create form based on block schema
    if (block.schema) {
      this.createSchemaForm(configForm, block.schema);
    } else {
      this.createDefaultForm(configForm, block);
    }
  }

  private createSchemaForm(container: HTMLElement, schema: any): void {
    Object.keys(schema).forEach(key => {
      const field = schema[key];
      const formGroup = this.createFormGroup(key, field);
      container.appendChild(formGroup);
    });
  }

  private createDefaultForm(container: HTMLElement, _block: any): void {
    const defaultFields = [
      { key: 'title', type: 'text', label: 'Title' },
      { key: 'content', type: 'textarea', label: 'Content' },
      { key: 'style', type: 'text', label: 'Style' }
    ];

    defaultFields.forEach(field => {
      const formGroup = this.createFormGroup(field.key, field);
      container.appendChild(formGroup);
    });
  }

  private createFormGroup(key: string, field: any): HTMLElement {
    const group = document.createElement('div');
    group.className = 'form-group';

    const label = document.createElement('label');
    label.textContent = field.label || key;
    label.setAttribute('for', key);

    let input: HTMLElement;
    
    switch (field.type) {
      case 'textarea':
        input = document.createElement('textarea');
        input.setAttribute('rows', '3');
        break;
      case 'select':
        input = document.createElement('select');
        if (field.options) {
          field.options.forEach((option: any) => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value || option;
            optionElement.textContent = option.label || option;
            input.appendChild(optionElement);
          });
        }
        break;
      default:
        input = document.createElement('input');
        input.setAttribute('type', field.type || 'text');
    }

    input.setAttribute('id', key);
    input.setAttribute('name', key);
    input.setAttribute('value', field.default || '');

    if (field.placeholder) {
      input.setAttribute('placeholder', field.placeholder);
    }

    group.appendChild(label);
    group.appendChild(input);

    return group;
  }

  private showEmptyConfig(): void {
    const configForm = document.getElementById('config-form');
    if (!configForm) return;

    configForm.innerHTML = `
      <div class="text-center py-10 text-slate-400">
        <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
        <p>Please select a block</p>
      </div>
    `;
  }

  private switchToConfigTab(): void {
    const configTab = document.getElementById('config-tab');
    if (configTab) {
      configTab.click();
    }
  }

  public getCurrentConfig(): any {
    if (!this.currentBlock) return null;

    const form = document.getElementById('config-form');
    if (!form) return null;

    const formData = new FormData(form as HTMLFormElement);
    const config: any = {};

    for (const [key, value] of formData.entries()) {
      config[key] = value;
    }

    return config;
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new ConfigManager();
});
