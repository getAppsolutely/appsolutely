/**
 * Scroll-triggered animation utilities
 * Uses IntersectionObserver to add/remove classes when elements enter/leave viewport
 */

export interface ObserveInViewOptions {
    threshold?: number;
    rootMargin?: string;
}

/**
 * Observe elements and toggle an in-view class when they intersect the viewport.
 *
 * @param selector - CSS selector for elements to observe
 * @param options - IntersectionObserver options (threshold, rootMargin)
 */
export function observeInView(selector: string, options: ObserveInViewOptions = {}): void {
    const elements = document.querySelectorAll<HTMLElement>(selector);
    if (!elements.length) return;

    const { threshold = 0.2, rootMargin = '0px' } = options;

    const observer = new IntersectionObserver(
        (entries: IntersectionObserverEntry[]) => {
            entries.forEach((entry: IntersectionObserverEntry) => {
                entry.target.classList.toggle('in-view', entry.isIntersecting);
            });
        },
        { root: null, rootMargin, threshold }
    );

    elements.forEach((el) => observer.observe(el));
}

export interface ObserveInViewWithStaggerOptions extends ObserveInViewOptions {
    titleSelector?: string;
    itemSelector?: string;
    staggerDelay?: number;
    initialDelay?: number;
}

/**
 * Observe elements, toggle in-view class, and apply staggered animations to child elements.
 *
 * @param selector - CSS selector for container elements to observe
 * @param options - Options for threshold, rootMargin, and staggered child selectors
 */
export function observeInViewWithStagger(selector: string, options: ObserveInViewWithStaggerOptions = {}): void {
    const elements = document.querySelectorAll<HTMLElement>(selector);
    if (!elements.length) return;

    const {
        threshold = 0.2,
        rootMargin = '0px',
        titleSelector = '',
        itemSelector = '',
        staggerDelay = 150,
        initialDelay = 200,
    } = options;

    const observer = new IntersectionObserver(
        (entries: IntersectionObserverEntry[]) => {
            entries.forEach((entry: IntersectionObserverEntry) => {
                const target = entry.target as HTMLElement;
                const isInView = entry.isIntersecting;

                target.classList.toggle('in-view', isInView);

                if (titleSelector) {
                    const title = target.querySelector<HTMLElement>(titleSelector);
                    title?.classList.toggle('animate', isInView);
                }

                if (itemSelector) {
                    const items = target.querySelectorAll<HTMLElement>(itemSelector);
                    items.forEach((item, index) => {
                        if (isInView) {
                            setTimeout(() => item.classList.add('animate'), initialDelay + index * staggerDelay);
                        } else {
                            item.classList.remove('animate');
                        }
                    });
                }
            });
        },
        { root: null, rootMargin, threshold }
    );

    elements.forEach((el) => observer.observe(el));
}
