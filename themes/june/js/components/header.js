/**
 * Header Component JavaScript
 * Handles scroll detection, hover effects, and mobile menu functionality
 */

class Header {
    constructor() {
        this.header = document.querySelector('.header');
        this.navbar = this.header?.querySelector('.navbar');
        this.navbarToggler = this.header?.querySelector('.navbar-toggler');
        this.navbarCollapse = this.header?.querySelector('.navbar-collapse');

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
        const dropdownItems = this.header?.querySelectorAll('.dropdown');

        dropdownItems?.forEach(item => {
            const dropdownMenu = item.querySelector('.dropdown-menu');

            if (dropdownMenu) {
                // Desktop hover effect
                if (window.innerWidth >= 992) {
                    item.addEventListener('mouseenter', () => {
                        this.showDropdown(dropdownMenu);
                    });

                    item.addEventListener('mouseleave', () => {
                        this.hideDropdown(dropdownMenu);
                    });
                }

                // Mobile click effect
                if (window.innerWidth < 992) {
                    const dropdownToggle = item.querySelector('.dropdown-toggle');

                    dropdownToggle?.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        // Close other dropdowns
                        dropdownItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.remove('show');
                            }
                        });

                        // Toggle current dropdown
                        item.classList.toggle('show');
                    });
                }
            }
        });
    }

    showDropdown(dropdownMenu) {
        dropdownMenu.style.opacity = '1';
        dropdownMenu.style.visibility = 'visible';
        dropdownMenu.style.transform = 'translateY(0)';
    }

    hideDropdown(dropdownMenu) {
        dropdownMenu.style.opacity = '0';
        dropdownMenu.style.visibility = 'hidden';
        dropdownMenu.style.transform = 'translateY(-100%)';
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
