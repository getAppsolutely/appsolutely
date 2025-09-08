// Photo Gallery client-side filtering

document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('gallery-grid');
    if (!grid) return;

    const PLACEHOLDER_URL = 'https://placehold.co/500x500?text=Coming+Soon';

    /**
     * @typedef {{url:string,title?:string,subtitle?:string,alt?:string,caption?:string,link?:string, category?:string, tags?:string[]}} Photo
     */

    /** @type {Photo[]} */
    const photos = (() => {
        try { return JSON.parse(grid.getAttribute('data-photos') || '[]'); } catch { return []; }
    })();

    const toTags = (photo) => {
        if (Array.isArray(photo.tags)) return photo.tags.filter(Boolean).map(String);
        if (typeof photo.category === 'string' && photo.category.trim() !== '') return [photo.category.trim()];
        return [];
    };

    // Build category order based on first occurrence in photos array
    const categoryOrder = [];
    const seenCategories = new Set();
    
    photos.forEach(photo => {
        const categories = toTags(photo);
        categories.forEach(category => {
            if (!seenCategories.has(category)) {
                categoryOrder.push(category);
                seenCategories.add(category);
            }
        });
    });

    const tags = categoryOrder;

    const filtersEl = document.getElementById('gallery-filters');
    const cardTpl = document.getElementById('gallery-card-template');

    const buildCard = (p) => {
        const node = cardTpl.content.firstElementChild.cloneNode(true);
        const img = node.querySelector('img');
        const title = node.querySelector('.card-title');
        const subtitle = node.querySelector('.card-subtitle');
        const text = node.querySelector('.card-text');
        const price = node.querySelector('.card-price');

        // Use placeholder when url is empty or missing; also handle load errors
        img.src = (p.url && String(p.url).trim() !== '') ? p.url : PLACEHOLDER_URL;
        img.alt = p.alt || p.title || '';
        img.addEventListener('error', () => {
            if (img.src !== PLACEHOLDER_URL) {
                img.src = PLACEHOLDER_URL;
            }
        }, { once: true });
        title.textContent = p.title || '';
        subtitle.textContent = p.subtitle || '';
        // prefer description from JSON, fallback to caption
        text.innerHTML = p.description || p.caption || '';

        // Price label with graceful hide when absent
        if (p.price && String(p.price).trim() !== '') {
            price.innerHTML = `<span class="text-muted me-1">Price:</span> ${String(p.price).trim()}`;
            price.classList.remove('d-none');
        } else {
            price.textContent = '';
            price.classList.add('d-none');
        }

        if (p.link) {
            const linkWrap = document.createElement('a');
            linkWrap.href = p.link;
            linkWrap.className = 'stretched-link';
            node.querySelector('.card').appendChild(linkWrap);
        }

        // for client-side filtering
        const tags = toTags(p);
        if (tags.length) node.firstElementChild?.setAttribute('data-tags', tags.join(','));

        return node;
    };

    const renderGrid = (filter = 'all') => {
        grid.innerHTML = '';
        let list = filter === 'all' ? photos : photos.filter(p => {
            const t = toTags(p);
            return t.includes(filter);
        });
        
        // Sort photos by category order when displaying
        list = list.sort((a, b) => {
            const aCategory = a.category || '';
            const bCategory = b.category || '';
            const aIndex = categoryOrder.indexOf(aCategory);
            const bIndex = categoryOrder.indexOf(bCategory);
            
            // If both are in the defined order, sort by their position
            if (aIndex !== -1 && bIndex !== -1) {
                return aIndex - bIndex;
            }
            // If only one is in the defined order, prioritize it
            if (aIndex !== -1) return -1;
            if (bIndex !== -1) return 1;
            // If neither is in the defined order, sort alphabetically
            return aCategory.localeCompare(bCategory);
        });
        
        list.forEach(p => grid.appendChild(buildCard(p)));
    };

    const renderFilters = () => {
        if (!filtersEl) return;
        const makeBtn = (label, value, active) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `btn btn-lg px-4 py-2 ${active ? 'btn-dark' : 'btn-outline-dark'}`;
            btn.textContent = label;
            btn.setAttribute('data-filter', value);
            btn.setAttribute('aria-pressed', String(active));
            btn.addEventListener('click', () => {
                // toggle active
                filtersEl.querySelectorAll('button').forEach(b => {
                    const isActive = b === btn;
                    b.classList.toggle('btn-dark', isActive);
                    b.classList.toggle('btn-outline-dark', !isActive);
                    b.setAttribute('aria-pressed', String(isActive));
                });
                renderGrid(value);
            });
            return btn;
        };

        // Clear & add buttons
        filtersEl.innerHTML = '';
        filtersEl.appendChild(makeBtn('All', 'all', true));
        tags.forEach(t => filtersEl.appendChild(makeBtn(t, t, false)));
    };

    renderFilters();
    renderGrid('all');
});


