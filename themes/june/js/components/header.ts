/**
 * Header Component JavaScript
 * Handles hover effects, mobile menu, and dropdown functionality.
 * Scroll state (.scrolled) is handled by IntersectionObserver on #scrollTrigger.
 */

import _ from 'lodash';
import type { HeaderInstance } from '../types';

export class Header implements HeaderInstance {
    header: HTMLElement | null;
    navbar: HTMLElement | null;
    navbarToggler: HTMLElement | null;
    navbarCollapse: HTMLElement | null;
    submenuItems: NodeListOf<Element> | null;

    private abortController: AbortController;

    constructor() {
        this.header = document.querySelector<HTMLElement>('#main-header');
        this.navbar = this.header?.querySelector<HTMLElement>('.navbar') ?? null;
        this.navbarToggler = this.header?.querySelector<HTMLElement>('.navbar-toggler') ?? null;
        this.navbarCollapse = this.header?.querySelector<HTMLElement>('.navbar-collapse') ?? null;
        this.submenuItems = this.header?.querySelectorAll('.has-submenu') ?? null;
        this.abortController = new AbortController();

        this.init();
    }

    init(): void {
        if (!this.header) return;

        this.bindEvents();
    }

    bindEvents(): void {
        const { signal } = this.abortController;

        // Mobile menu toggle
        if (this.navbarToggler && this.navbarCollapse) {
            this.navbarToggler.addEventListener('click', () => this.toggleMobileMenu(), { signal });
        }

        // Close mobile menu when clicking outside
        document.addEventListener(
            'click',
            (e: MouseEvent) => {
                if (this.navbarCollapse?.classList.contains('show') && !this.navbar?.contains(e.target as Node)) {
                    this.closeMobileMenu();
                }
            },
            { signal }
        );

        // Handle dropdown hover effects (desktop) and click (mobile)
        this.handleDropdownHover(signal);
    }

    destroy(): void {
        this.abortController.abort();
    }

    toggleMobileMenu(): void {
        if (this.navbarCollapse && this.navbarToggler) {
            this.navbarCollapse.classList.toggle('show');

            // Update aria-expanded
            const isExpanded = this.navbarCollapse.classList.contains('show');
            this.navbarToggler.setAttribute('aria-expanded', String(isExpanded));
        }
    }

    closeMobileMenu(): void {
        if (this.navbarCollapse && this.navbarToggler) {
            this.navbarCollapse.classList.remove('show');
            this.navbarToggler.setAttribute('aria-expanded', 'false');
        }
    }

    handleDropdownHover(signal: AbortSignal): void {
        this.submenuItems?.forEach((item: Element) => {
            const submenu = item.querySelector<HTMLElement>('.submenu');
            let hoverTimeout: ReturnType<typeof setTimeout>;

            if (submenu) {
                // Desktop hover effect
                if (window.innerWidth >= 1200) {
                    item.addEventListener(
                        'mouseenter',
                        () => {
                            clearTimeout(hoverTimeout);
                            this.showMegaMenu(submenu);
                            item.classList.add('active');
                        },
                        { signal }
                    );
                    item.addEventListener(
                        'mouseleave',
                        () => {
                            hoverTimeout = setTimeout(() => {
                                this.hideMegaMenu(submenu);
                                item.classList.remove('active');
                            }, 150);
                        },
                        { signal }
                    );
                }

                // Mobile click effect
                if (window.innerWidth < 1200) {
                    const navLink = item.querySelector<HTMLElement>('.nav-link');
                    navLink?.addEventListener(
                        'click',
                        (e: Event) => {
                            e.preventDefault();
                            e.stopPropagation();
                            this.submenuItems?.forEach((otherItem: Element) => {
                                if (otherItem !== item) {
                                    otherItem.classList.remove('show');
                                }
                            });
                            item.classList.toggle('show');
                        },
                        { signal }
                    );
                }
            }
        });
    }

    showMegaMenu(submenu: HTMLElement): void {
        submenu.style.display = 'block';
        // Force reflow to ensure display:block takes effect
        submenu.offsetHeight;
        submenu.style.opacity = '1';
        submenu.style.pointerEvents = 'auto';
        submenu.style.transform = 'translateY(0)';
    }

    hideMegaMenu(submenu: HTMLElement): void {
        submenu.style.opacity = '0';
        submenu.style.pointerEvents = 'none';
        submenu.style.transform = 'translateY(-10px)';

        // Hide after transition completes
        setTimeout(() => {
            if (submenu.style.opacity === '0') {
                submenu.style.display = 'none';
            }
        }, 400);
    }
}

// Single header instance; recreated on resize with proper cleanup
let headerInstance: Header | null = null;

function initHeader(): void {
    headerInstance?.destroy();
    headerInstance = new Header();
}

document.addEventListener('DOMContentLoaded', () => {
    initHeader();

    // Scroll state: add .scrolled when #scrollTrigger leaves viewport
    const trigger = document.getElementById('scrollTrigger');
    const navbar = document.getElementById('main-header');
    if (trigger && navbar) {
        const observer = new IntersectionObserver(
            ([entry]: IntersectionObserverEntry[]) => {
                navbar.classList.toggle('scrolled', !entry.isIntersecting);
            },
            { rootMargin: '0px', threshold: 0 }
        );
        observer.observe(trigger);
    }

    // Reinitialize on resize (dropdown behavior depends on viewport width); debounced to avoid leak
    window.addEventListener('resize', _.debounce(initHeader, 150));
});
