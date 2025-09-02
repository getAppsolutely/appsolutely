// Store Locations Dropdown Component
class StoreLocationsDropdown {
    constructor() {
        this.elements = {
            display: document.getElementById('selected-location-display'),
            noSelection: document.getElementById('no-selection-message'),
            select: document.getElementById('store-location-select')
        };
    }

    // Utility function to show/hide elements
    toggleElement(id, show, displayType = 'block') {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = show ? displayType : 'none';
        }
    }

    // Utility function to update text content
    updateText(id, content) {
        const element = document.getElementById(id);
        if (element && content) {
            element.textContent = content;
            element.style.display = 'block';
        } else if (element) {
            element.style.display = 'none';
        }
    }

    // Utility function to update link
    updateLink(id, href, text, displayType = 'flex') {
        const element = document.getElementById(id);
        const linkId = id.replace('-section', '').replace('phone', 'selected-location-phone').replace('email', 'selected-location-email').replace('website', 'selected-location-website');
        const link = document.getElementById(linkId);

        if (element && link && href) {
            link.href = href;
            link.textContent = text;
            element.style.display = displayType;
        } else if (element) {
            element.style.display = 'none';
        }
    }

        // Generate hours table HTML with section title
    generateHoursTable(hours, sectionTitle = '') {
        if (!hours) return '';

        // Split by comma with optional spaces (matching PHP preg_split('/\s*,\s*/', ...))
        const lines = hours.split(/\s*,\s*/).filter(line => line.trim());
        const rows = lines.map(line => {
            // Split on first colon to separate day from time
            const colonIndex = line.indexOf(':');
            if (colonIndex === -1) {
                // No colon found, treat entire line as day
                return `<tr><td class="pe-2 text-nowrap">${line.trim()}</td><td class="ps-2 text-muted">-</td></tr>`;
            }

            const day = line.substring(0, colonIndex).trim();
            const time = line.substring(colonIndex + 1).trim();

            return `<tr><td class="pe-2 text-nowrap">${day}</td><td class="ps-2 text-muted">${time.replaceAll(':00 ', '').toLowerCase()}</td></tr>`;
        }).join('');

        const titleHtml = sectionTitle ? `<div class="small mb-1 fw-semibold">${sectionTitle}</div>` : '';
        const tableHtml = `<table class="table table-sm table-borderless mb-0 align-middle w-auto"><tbody>${rows}</tbody></table>`;

        return `${titleHtml}<div class="d-flex align-items-start mb-3"><i class="fas fa-clock text-muted me-3 mt-1"></i><div class="small text-muted lh-sm w-100">${tableHtml}</div></div>`;
    }

    // Generate services badges HTML
    generateServicesBadges(services) {
        if (!services?.length) return '';
        return services.map(service => `<span class="badge bg-light text-dark border">${service}</span>`).join('');
    }

    // Update location display
    updateLocation(location) {
        // Basic info
        this.updateText('selected-location-name', location.name);
        this.updateText('selected-location-type', location.type);

        // Featured badge
        this.toggleElement('featured-badge', location.featured);

        // Address
        this.updateText('selected-location-address', location.address);
        const cityParts = [location.city, location.state, location.zip_code].filter(Boolean);
        this.updateText('selected-location-city', cityParts.join(', '));

        // Contact info
        this.updateLink('phone-section', `tel:${location.phone}`, location.phone);
        this.updateLink('email-section', `mailto:${location.email}`, location.email);
        this.updateLink('website-section', location.website, location.website?.replace(/^https?:\/\//, ''));

                // Hours
        const hoursHtml = this.generateHoursTable(location.hours, 'Vehicle Sales');
        const hoursDiv = document.getElementById('selected-location-hours');
        if (hoursDiv) {
            hoursDiv.innerHTML = hoursHtml;
            this.toggleElement('hours-section', !!location.hours);
        }

        // Service hours
        const serviceHoursHtml = this.generateHoursTable(location.service_hours, 'Servicing & Parts');
        const serviceHoursDiv = document.getElementById('selected-location-service-hours');
        if (serviceHoursDiv) {
            serviceHoursDiv.innerHTML = serviceHoursHtml;
            this.toggleElement('service-hours-section', !!location.service_hours);
        }

        // Services
        const servicesHtml = this.generateServicesBadges(location.services);
        const servicesDiv = document.getElementById('selected-location-services');
        if (servicesDiv) {
            servicesDiv.innerHTML = servicesHtml;
            this.toggleElement('services-section', !!location.services?.length);
        }

        // Additional info
        this.updateText('selected-location-manager', location.manager);
        this.updateText('selected-location-established', location.established);
        this.toggleElement('manager-section', !!location.manager);
        this.toggleElement('established-section', !!location.established);
        this.toggleElement('additional-info', !!(location.manager || location.established));
    }

    // Main function to show selected location
    showSelected(locationIndex) {
        if (!locationIndex) {
            this.toggleElement('selected-location-display', false);
            this.toggleElement('no-selection-message', true);
            return;
        }

        const selectedOption = this.elements.select.options[this.elements.select.selectedIndex];
        const location = JSON.parse(selectedOption.getAttribute('data-location'));

        this.toggleElement('no-selection-message', false);
        this.toggleElement('selected-location-display', true);
        this.updateLocation(location);
    }
}

// Initialize component
const storeLocationsDropdown = new StoreLocationsDropdown();

// Make function globally accessible
window.showSelectedLocation = (locationIndex) => storeLocationsDropdown.showSelected(locationIndex);

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    storeLocationsDropdown.toggleElement('no-selection-message', true);
    storeLocationsDropdown.toggleElement('selected-location-display', false);
});
