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

// Extend Window interface for June-specific globals (_, bootstrap, axios, assets, asset_url, etc. are declared in types/global.d.ts and themes/june/js/utils/asset-url.ts)
declare global {
    interface Window {
        lazyManager?: {
            update(): void;
        };
    }
}

export {};
