/**
 * Header Component Tests
 * Tests for the header component functionality
 */

import { describe, it, expect, beforeEach } from 'vitest';
import { Header } from '@themes/june/js/components/header';

describe('Header Component', () => {
    let headerElement: HTMLElement;
    let navbarToggler: HTMLElement;
    let navbarCollapse: HTMLElement;

    beforeEach(() => {
        // Setup DOM
        document.body.innerHTML = `
      <div id="scrollTrigger"></div>
      <header id="main-header">
        <nav class="navbar">
          <button class="navbar-toggler" aria-expanded="false">Toggle</button>
          <div class="navbar-collapse"></div>
        </nav>
      </header>
    `;

        headerElement = document.getElementById('main-header') as HTMLElement;
        navbarToggler = headerElement.querySelector('.navbar-toggler') as HTMLElement;
        navbarCollapse = headerElement.querySelector('.navbar-collapse') as HTMLElement;
    });

    it('should initialize header component', () => {
        const header = new Header();
        expect(header.header).toBeTruthy();
        expect(header.navbar).toBeTruthy();
    });

    it('should add scrolled class when scrolling', () => {
        const header = new Header();

        // Mock scroll position
        Object.defineProperty(window, 'pageYOffset', {
            writable: true,
            value: 100,
        });
        Object.defineProperty(document.documentElement, 'scrollTop', {
            writable: true,
            value: 100,
        });

        header.checkScroll();
        expect(headerElement.classList.contains('scrolled')).toBe(true);
    });

    it('should toggle mobile menu', () => {
        const header = new Header();

        expect(navbarCollapse.classList.contains('show')).toBe(false);

        header.toggleMobileMenu();
        expect(navbarCollapse.classList.contains('show')).toBe(true);
        expect(navbarToggler.getAttribute('aria-expanded')).toBe('true');

        header.toggleMobileMenu();
        expect(navbarCollapse.classList.contains('show')).toBe(false);
        expect(navbarToggler.getAttribute('aria-expanded')).toBe('false');
    });

    it('should close mobile menu', () => {
        const header = new Header();

        navbarCollapse.classList.add('show');
        navbarToggler.setAttribute('aria-expanded', 'true');

        header.closeMobileMenu();

        expect(navbarCollapse.classList.contains('show')).toBe(false);
        expect(navbarToggler.getAttribute('aria-expanded')).toBe('false');
    });
});
