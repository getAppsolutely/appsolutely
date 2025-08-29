/**
 * Text Document Collapsible Component
 * Enhances Bootstrap 5.3 collapse functionality
 */
class TextDocumentCollapsible {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAccessibility();
    }

    bindEvents() {
        // Listen for Bootstrap collapse events
        document.addEventListener('shown.bs.collapse', (event) => {
            this.handleCollapseShown(event);
        });

        document.addEventListener('hidden.bs.collapse', (event) => {
            this.handleCollapseHidden(event);
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', (event) => {
            this.handleKeyboardNavigation(event);
        });
    }

    handleCollapseShown(event) {
        const target = event.target;
        const header = target.previousElementSibling;
        
        if (header && header.classList.contains('text-document-collapsible__header')) {
            // Update ARIA attributes
            header.setAttribute('aria-expanded', 'true');
            
            // Add active class for styling
            header.classList.add('text-document-collapsible__header--active');
            
            // Focus management for accessibility
            this.focusFirstFocusableElement(target);
        }
    }

    handleCollapseHidden(event) {
        const target = event.target;
        const header = target.previousElementSibling;
        
        if (header && header.classList.contains('text-document-collapsible__header')) {
            // Update ARIA attributes
            header.setAttribute('aria-expanded', 'false');
            
            // Remove active class
            header.classList.remove('text-document-collapsible__header--active');
        }
    }

    handleKeyboardNavigation(event) {
        const target = event.target;
        
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

    toggleCollapse(header) {
        const targetId = header.getAttribute('data-bs-target');
        const target = document.querySelector(targetId);
        
        if (target) {
            const bsCollapse = new bootstrap.Collapse(target, {
                toggle: true
            });
        }
    }

    collapseAll() {
        const openCollapses = document.querySelectorAll('.text-document-collapsible__content.show');
        
        openCollapses.forEach(collapse => {
            const bsCollapse = new bootstrap.Collapse(collapse, {
                hide: true
            });
        });
    }

    focusFirstFocusableElement(container) {
        const focusableElements = container.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    setupAccessibility() {
        const headers = document.querySelectorAll('.text-document-collapsible__header');
        
        headers.forEach(header => {
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
    static toggle(blockId) {
        const header = document.querySelector(`[data-bs-target="#textDocumentCollapsible${blockId}"]`);
        if (header) {
            const targetId = header.getAttribute('data-bs-target');
            const target = document.querySelector(targetId);
            
            if (target) {
                const bsCollapse = new bootstrap.Collapse(target, {
                    toggle: true
                });
            }
        }
    }

    // Public method to expand all
    static expandAll() {
        const headers = document.querySelectorAll('.text-document-collapsible__header[aria-expanded="false"]');
        
        headers.forEach(header => {
            const targetId = header.getAttribute('data-bs-target');
            const target = document.querySelector(targetId);
            
            if (target) {
                const bsCollapse = new bootstrap.Collapse(target, {
                    show: true
                });
            }
        });
    }

    // Public method to collapse all
    static collapseAll() {
        const openCollapses = document.querySelectorAll('.text-document-collapsible__content.show');
        
        openCollapses.forEach(collapse => {
            const bsCollapse = new bootstrap.Collapse(collapse, {
                hide: true
            });
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TextDocumentCollapsible();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TextDocumentCollapsible;
}
