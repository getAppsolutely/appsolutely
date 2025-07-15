/**
 * Header Component JavaScript
 * Handles scroll detection, hover effects, and mobile menu functionality
 */

class Header {
    constructor() {
        this.header = document.querySelector('#mainHeader');
        this.navbar = this.header?.querySelector('.navbar');
        this.navbarToggler = this.header?.querySelector('.navbar-toggler');
        this.navbarCollapse = this.header?.querySelector('.navbar-collapse');
        this.submenuItems = this.header?.querySelectorAll('.has-submenu');

        this.init();
    }

    init() {
        if (!this.header) return;

        this.bindEvents();
        this.checkScroll();
    }

    bindEvents() {
        // Scroll event for header background change
        window.addEventListener('scroll', () => {
            this.checkScroll();
        });

        // Mobile menu toggle
        if (this.navbarToggler && this.navbarCollapse) {
            this.navbarToggler.addEventListener('click', () => {
                this.toggleMobileMenu();
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.navbarCollapse?.classList.contains('show') &&
                !this.navbar?.contains(e.target)) {
                this.closeMobileMenu();
            }
        });

        // Handle dropdown hover effects
        this.handleDropdownHover();
    }

    checkScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > 50) {
            this.header.classList.add('scrolled');
        } else {
            this.header.classList.remove('scrolled');
        }
    }

    toggleMobileMenu() {
        if (this.navbarCollapse) {
            this.navbarCollapse.classList.toggle('show');

            // Update aria-expanded
            const isExpanded = this.navbarCollapse.classList.contains('show');
            this.navbarToggler.setAttribute('aria-expanded', isExpanded);
        }
    }

    closeMobileMenu() {
        if (this.navbarCollapse) {
            this.navbarCollapse.classList.remove('show');
            this.navbarToggler.setAttribute('aria-expanded', 'false');
        }
    }

    handleDropdownHover() {
        // Handle mega menu hover effects
        this.submenuItems?.forEach(item => {
            const submenu = item.querySelector('.submenu');
            let hoverTimeout;

            if (submenu) {
                // Desktop hover effect
                if (window.innerWidth >= 1200) {
                    item.addEventListener('mouseenter', () => {
                        clearTimeout(hoverTimeout);
                        this.showMegaMenu(submenu);
                        // Add active class to parent nav item
                        item.classList.add('active');
                    });

                    item.addEventListener('mouseleave', () => {
                        // Add small delay before hiding to prevent flickering
                        hoverTimeout = setTimeout(() => {
                            this.hideMegaMenu(submenu);
                            item.classList.remove('active');
                        }, 150);
                    });
                }

                // Mobile click effect
                if (window.innerWidth < 1200) {
                    const navLink = item.querySelector('.nav-link');

                    navLink?.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        // Close other submenus
                        this.submenuItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.remove('show');
                            }
                        });

                        // Toggle current submenu
                        item.classList.toggle('show');
                    });
                }
            }
        });
    }

    showMegaMenu(submenu) {
        submenu.style.display = 'block';
        // Force reflow to ensure display:block takes effect
        submenu.offsetHeight;
        submenu.style.opacity = '1';
        submenu.style.pointerEvents = 'auto';
        submenu.style.transform = 'translateY(0)';
    }

    hideMegaMenu(submenu) {
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

// Initialize header when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Header();
});

// Handle window resize
window.addEventListener('resize', () => {
    // Reinitialize header functionality on resize
    setTimeout(() => {
        new Header();
    }, 100);
});

(() => {
    const trigger = document.getElementById('scrollTrigger');
    const navbar = document.getElementById('mainHeader');

    if (!trigger || !navbar) return console.warn('[navbar-scroll] Missing required elements');

    const observer = new IntersectionObserver(([entry]) => {

        if (!entry.isIntersecting) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }, {
        rootMargin: '0px',
        threshold: 0
    });

    observer.observe(trigger);
})();
