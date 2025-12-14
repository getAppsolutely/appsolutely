/**
 * Dynamic Form Interactive Component
 * Handles dynamic background image switching based on form field selections
 */

interface OptionsMapping {
    [key: string]: string;
}

interface LivewireComponent {
    set(property: string, value: string): void;
}

interface LivewireGlobal {
    find(id: string): LivewireComponent | undefined;
}

declare global {
    interface Window {
        Livewire?: LivewireGlobal;
    }
}

class DynamicFormInteractive {
    private backgroundImageField: HTMLInputElement | null = null;
    private backgroundContainer: HTMLElement | null = null;
    private optionsMapping: OptionsMapping = {};
    private triggerField: HTMLSelectElement | null = null;

    constructor() {
        this.init();
    }

    init(): void {
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeForm();
            });
        } else {
            this.initializeForm();
        }

        // Re-initialize on Livewire updates
        if (window.Livewire) {
            document.addEventListener('livewire:load', () => {
                this.initializeForm();
            });

            document.addEventListener('livewire:update', () => {
                setTimeout(() => {
                    this.initializeForm();
                }, 100);
            });
        }
    }

    private initializeForm(): void {
        // Find the background image hidden field
        this.backgroundImageField = document.querySelector<HTMLInputElement>('input[type="hidden"]#background_image');
        this.backgroundContainer = document.querySelector<HTMLElement>(
            '#vehicle-background-container .vehicle-background-image'
        );

        if (!this.backgroundImageField || !this.backgroundContainer) {
            return;
        }

        // Get options mapping from hidden field
        const mappingAttr = this.backgroundImageField.getAttribute('data-options-mapping') || '{}';
        try {
            this.optionsMapping = JSON.parse(mappingAttr) as OptionsMapping;
        } catch (e) {
            console.error('Failed to parse options mapping:', e);
            return;
        }

        if (Object.keys(this.optionsMapping).length === 0) {
            return; // No mapping available
        }

        // Find the trigger field
        this.findTriggerField();

        if (this.triggerField) {
            this.setupEventListeners();
            this.initializeBackground();
        }
    }

    private findTriggerField(): void {
        // Find all select fields that might trigger background changes
        const selectFields = document.querySelectorAll<HTMLSelectElement>('select[data-field-name]');

        // Find the field that matches the mapping keys (likely vehicle_interest)
        selectFields.forEach((field) => {
            // Check if any of this field's options match keys in the mapping
            Array.from(field.options).forEach((option) => {
                if (option.value && Object.prototype.hasOwnProperty.call(this.optionsMapping, option.value)) {
                    this.triggerField = field;
                }
            });
        });

        // Fallback: try to find vehicle_interest specifically
        if (!this.triggerField) {
            this.triggerField = document.querySelector<HTMLSelectElement>('select#vehicle_interest');
        }
    }

    private setupEventListeners(): void {
        if (!this.triggerField) {
            return;
        }

        this.triggerField.addEventListener('change', () => {
            if (this.triggerField?.value) {
                this.updateBackgroundImage(this.triggerField.value);
            }
        });
    }

    private initializeBackground(): void {
        if (this.triggerField?.value) {
            this.updateBackgroundImage(this.triggerField.value);
        }
    }

    private updateBackgroundImage(selectedValue: string): void {
        if (!this.backgroundImageField || !this.backgroundContainer) {
            return;
        }

        if (!Object.prototype.hasOwnProperty.call(this.optionsMapping, selectedValue)) {
            return;
        }

        const imageUrl = this.optionsMapping[selectedValue];
        this.backgroundImageField.value = imageUrl;

        // Update the background image with fade effect
        this.backgroundContainer.style.opacity = '0';
        setTimeout(() => {
            if (this.backgroundContainer) {
                this.backgroundContainer.style.backgroundImage = `url(${imageUrl})`;
                this.backgroundContainer.style.opacity = '1';
            }
        }, 250);

        // Trigger Livewire update
        this.updateLivewireField(imageUrl);
    }

    private updateLivewireField(imageUrl: string): void {
        if (!window.Livewire?.find) {
            return;
        }

        const wireIdElement = document.querySelector('[wire\\:id]');
        const wireId = wireIdElement?.getAttribute('wire:id');

        if (wireId) {
            const component = window.Livewire.find(wireId);
            if (component) {
                component.set('formData.background_image', imageUrl);
            }
        }
    }
}

// Initialize the component
new DynamicFormInteractive();

// Export to make this a module (required for declare global)
export {};
