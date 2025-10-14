/**
 * Text Document Collapsible Component
 * Enhances Bootstrap 5.3 collapse functionality
 */

class TextDocumentCollapsible {
    constructor() {
        this.init();
    }

    init(): void {
        this.bindEvents();
        this.setupAccessibility();
    }

    bindEvents(): void {
        // Listen for Bootstrap collapse events
        document.addEventListener('shown.bs.collapse', (event: Event) => {
            this.handleCollapseShown(event);
        });

        document.addEventListener('hidden.bs.collapse', (event: Event) => {
            this.handleCollapseHidden(event);
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', (event: KeyboardEvent) => {
            this.handleKeyboardNavigation(event);
        });
    }

    handleCollapseShown(event: Event): void {
        const target = event.target as HTMLElement;
        const header = target.previousElementSibling as HTMLElement | null;
        
        if (header && header.classList.contains('text-document-collapsible__header')) {
            // Update ARIA attributes
            header.setAttribute('aria-expanded', 'true');
            
            // Add active class for styling
            header.classList.add('text-document-collapsible__header--active');
            
            // Focus management for accessibility
            this.focusFirstFocusableElement(target);
        }
    }

    handleCollapseHidden(event: Event): void {
        const target = event.target as HTMLElement;
        const header = target.previousElementSibling as HTMLElement | null;
        
        if (header && header.classList.contains('text-document-collapsible__header')) {
            // Update ARIA attributes
            header.setAttribute('aria-expanded', 'false');
            
            // Remove active class
            header.classList.remove('text-document-collapsible__header--active');
        }
    }

    handleKeyboardNavigation(event: KeyboardEvent): void {
        const target = event.target as HTMLElement;
        
        // Only handle if target is a collapsible header
        if (!target.classList.contains('text-document-collapsible__header')) {
            return;
        }

        switch (event.key) {
            case 'Enter':
            case ' ':
                event.preventDefault();
                this.toggleCollapse(target);
                break;
                
            case 'Escape':
                this.collapseAll();
                break;
        }
    }

    toggleCollapse(header: HTMLElement): void {
        const targetId = header.getAttribute('data-bs-target');
        if (!targetId) return;
        
        const target = document.querySelector<HTMLElement>(targetId);
        
        if (target && window.bootstrap) {
            new window.bootstrap.Collapse(target, {
                toggle: true,
            });
        }
    }

    collapseAll(): void {
        const openCollapses = document.querySelectorAll<HTMLElement>('.text-document-collapsible__content.show');
        
        openCollapses.forEach((collapse: HTMLElement) => {
            if (window.bootstrap) {
                const bsCollapse = new window.bootstrap.Collapse(collapse, {
                    toggle: false,
                });
                bsCollapse.hide();
            }
        });
    }

    focusFirstFocusableElement(container: HTMLElement): void {
        const focusableElements = container.querySelectorAll<HTMLElement>(
            'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    setupAccessibility(): void {
        const headers = document.querySelectorAll<HTMLElement>('.text-document-collapsible__header');
        
        headers.forEach((header: HTMLElement) => {
            // Ensure proper ARIA attributes
            if (!header.hasAttribute('aria-expanded')) {
                header.setAttribute('aria-expanded', 'false');
            }
            
            if (!header.hasAttribute('role')) {
                header.setAttribute('role', 'button');
            }
            
            if (!header.hasAttribute('tabindex')) {
                header.setAttribute('tabindex', '0');
            }
        });
    }

    // Public method to programmatically expand/collapse
    static toggle(blockId: string): void {
        const header = document.querySelector<HTMLElement>(`[data-bs-target="#textDocumentCollapsible${blockId}"]`);
        if (header) {
            const targetId = header.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const target = document.querySelector<HTMLElement>(targetId);
            
            if (target && window.bootstrap) {
                new window.bootstrap.Collapse(target, {
                    toggle: true,
                });
            }
        }
    }

    // Public method to expand all
    static expandAll(): void {
        const headers = document.querySelectorAll<HTMLElement>('.text-document-collapsible__header[aria-expanded="false"]');
        
        headers.forEach((header: HTMLElement) => {
            const targetId = header.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const target = document.querySelector<HTMLElement>(targetId);
            
            if (target && window.bootstrap) {
                const bsCollapse = new window.bootstrap.Collapse(target, {
                    toggle: false,
                });
                bsCollapse.show();
            }
        });
    }

    // Public method to collapse all
    static collapseAll(): void {
        const openCollapses = document.querySelectorAll<HTMLElement>('.text-document-collapsible__content.show');
        
        openCollapses.forEach((collapse: HTMLElement) => {
            if (window.bootstrap) {
                const bsCollapse = new window.bootstrap.Collapse(collapse, {
                    toggle: false,
                });
                bsCollapse.hide();
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TextDocumentCollapsible();
});

// Export for module usage
export default TextDocumentCollapsible;

