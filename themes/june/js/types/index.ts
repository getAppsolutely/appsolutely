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

// Extend Window interface for global utilities
declare global {
    interface Window {
        _: any;
        bootstrap: any;
        axios: any;
        assets: Record<string, unknown>;
        lazyManager?: {
            update(): void;
        };
        asset_url(uri: string | null | undefined, withHash?: boolean): string;
        getAssetBaseUrl(): string;
        getBuildHash(): string;
    }
}

export {};
