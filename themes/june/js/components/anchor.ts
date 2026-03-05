/**
 * Anchor Navigation Component
 * Updates active state based on scroll position (scroll-spy)
 */

const ACTIVE_CLASS = 'anchor-nav__link--active';

function updateActiveLink(links: NodeListOf<HTMLAnchorElement>): void {
    const viewportTop = window.scrollY + 120; // Offset for sticky nav
    let activeHref: string | null = null;

    links.forEach((link) => {
        const href = link.getAttribute('href');
        if (!href || !href.startsWith('#block-')) return;

        const target = document.getElementById(href.slice(1));
        if (!target) return;

        const rect = target.getBoundingClientRect();
        const top = rect.top + window.scrollY;

        if (top <= viewportTop) {
            activeHref = href;
        }
    });

    // If none passed, use first
    if (!activeHref && links.length > 0) {
        const firstHref = links[0].getAttribute('href');
        if (firstHref) activeHref = firstHref;
    }

    links.forEach((link) => {
        if (link.getAttribute('href') === activeHref) {
            link.classList.add(ACTIVE_CLASS);
        } else {
            link.classList.remove(ACTIVE_CLASS);
        }
    });
}

function initAnchorNav(): void {
    const nav = document.querySelector<HTMLElement>('.anchor-nav');
    if (!nav) return;

    const links = nav.querySelectorAll<HTMLAnchorElement>('.anchor-nav__link[href^="#block-"]');
    if (links.length === 0) return;

    updateActiveLink(links);

    let ticking = false;
    window.addEventListener('scroll', () => {
        if (ticking) return;
        window.requestAnimationFrame(() => {
            updateActiveLink(links);
            ticking = false;
        });
        ticking = true;
    });

    links.forEach((link) => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (!href || !href.startsWith('#')) return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initAnchorNav);
