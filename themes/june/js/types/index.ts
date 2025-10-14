/**
 * June Theme Type Definitions
 */

// Asset types
export interface AssetPaths {
    images: {
        coming: string;
        rednote: string;
    };
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
    checkScroll(): void;
    toggleMobileMenu(): void;
    closeMobileMenu(): void;
    handleDropdownHover(): void;
    showMegaMenu(submenu: HTMLElement): void;
    hideMegaMenu(submenu: HTMLElement): void;
}

export {};

