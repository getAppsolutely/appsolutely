/**
 * June Theme Type Definitions
 */

/**
 * Component init function signature.
 * All theme components must export an init function matching this type.
 * Called by init.ts on DOMContentLoaded and livewire:navigated.
 */
export type ComponentInit = () => void;

// Asset types
export interface AssetPaths {
    images: {
        coming: string;
        rednote: string;
    };
}

// Photo gallery (from data-photos JSON)
export interface Photo {
    image_src: string;
    title?: string;
    subtitle?: string;
    description?: string;
    alt?: string;
    caption?: string;
    link?: string;
    category?: string;
    tags?: string[];
    price?: string;
}

// Component interfaces
export interface HeaderInstance {
    header: HTMLElement | null;
    navbar: HTMLElement | null;
    navbarToggler: HTMLElement | null;
    navbarCollapse: HTMLElement | null;
    submenuItems: NodeListOf<Element> | null;
    init(): void;
    bindEvents(): void;
    destroy(): void;
    toggleMobileMenu(): void;
    closeMobileMenu(): void;
    handleDropdownHover(signal: AbortSignal): void;
    showMegaMenu(submenu: HTMLElement): void;
    hideMegaMenu(submenu: HTMLElement): void;
}

export {};
