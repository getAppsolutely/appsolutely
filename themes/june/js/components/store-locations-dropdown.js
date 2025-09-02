// Make function globally accessible
window.showSelectedLocation = function(locationIndex) {
    const displayDiv = document.getElementById('selected-location-display');
    const noSelectionDiv = document.getElementById('no-selection-message');
    const selectElement = document.getElementById('store-location-select');
    
    if (!locationIndex || locationIndex === '') {
        displayDiv.style.display = 'none';
        noSelectionDiv.style.display = 'block';
        return;
    }
    
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const location = JSON.parse(selectedOption.getAttribute('data-location'));
    
    // Hide no selection message and show location details
    noSelectionDiv.style.display = 'none';
    displayDiv.style.display = 'block';
    
    // Update location name and type
    document.getElementById('selected-location-name').textContent = location.name;
    const typeElement = document.getElementById('selected-location-type');
    if (location.type) {
        typeElement.textContent = location.type;
        typeElement.style.display = 'block';
    } else {
        typeElement.style.display = 'none';
    }
    
    // Update featured badge
    const featuredBadge = document.getElementById('featured-badge');
    if (location.featured) {
        featuredBadge.classList.remove('d-none');
    } else {
        featuredBadge.classList.add('d-none');
    }
    
    // Update address
    document.getElementById('selected-location-address').textContent = location.address;
    const cityElement = document.getElementById('selected-location-city');
    let cityText = '';
    if (location.city) cityText += location.city;
    if (location.state) cityText += (cityText ? ', ' : '') + location.state;
    if (location.zip_code) cityText += (cityText ? ' ' : '') + location.zip_code;
    cityElement.textContent = cityText;
    
    // Update phone
    const phoneSection = document.getElementById('phone-section');
    const phoneLink = document.getElementById('selected-location-phone');
    if (location.phone) {
        phoneLink.href = 'tel:' + location.phone;
        phoneLink.textContent = location.phone;
        phoneSection.style.display = 'flex';
    } else {
        phoneSection.style.display = 'none';
    }
    
    // Update email
    const emailSection = document.getElementById('email-section');
    const emailLink = document.getElementById('selected-location-email');
    if (location.email) {
        emailLink.href = 'mailto:' + location.email;
        emailLink.textContent = location.email;
        emailSection.style.display = 'flex';
    } else {
        emailSection.style.display = 'none';
    }
    
    // Update website
    const websiteSection = document.getElementById('website-section');
    const websiteLink = document.getElementById('selected-location-website');
    if (location.website) {
        websiteLink.href = location.website;
        websiteLink.textContent = location.website.replace(/^https?:\/\//, '');
        websiteSection.style.display = 'flex';
    } else {
        websiteSection.style.display = 'none';
    }
    
    // Update hours
    const hoursSection = document.getElementById('hours-section');
    const hoursDiv = document.getElementById('selected-location-hours');
    if (location.hours) {
        const hoursLines = location.hours.split(',').map(line => line.trim());
        let hoursHtml = '<table class="table table-sm table-borderless mb-0 align-middle w-auto"><tbody>';
        hoursLines.forEach(line => {
            const parts = line.split(':');
            if (parts.length >= 2) {
                hoursHtml += `<tr><td class="pe-2 fw-semibold text-nowrap">${parts[0].trim()}</td><td class="ps-2 text-muted">${parts[1].trim()}</td></tr>`;
            }
        });
        hoursHtml += '</tbody></table>';
        hoursDiv.innerHTML = hoursHtml;
        hoursSection.style.display = 'block';
    } else {
        hoursSection.style.display = 'none';
    }
    
    // Update service hours
    const serviceHoursSection = document.getElementById('service-hours-section');
    const serviceHoursDiv = document.getElementById('selected-location-service-hours');
    if (location.service_hours) {
        const serviceHoursLines = location.service_hours.split(',').map(line => line.trim());
        let serviceHoursHtml = '<table class="table table-sm table-borderless mb-0 align-middle w-auto"><tbody>';
        serviceHoursLines.forEach(line => {
            const parts = line.split(':');
            if (parts.length >= 2) {
                serviceHoursHtml += `<tr><td class="pe-2 fw-semibold text-nowrap">${parts[0].trim()}</td><td class="ps-2 text-muted">${parts[1].trim()}</td></tr>`;
            }
        });
        serviceHoursHtml += '</tbody></table>';
        serviceHoursDiv.innerHTML = serviceHoursHtml;
        serviceHoursSection.style.display = 'block';
    } else {
        serviceHoursSection.style.display = 'none';
    }
    
    // Update services
    const servicesSection = document.getElementById('services-section');
    const servicesDiv = document.getElementById('selected-location-services');
    if (location.services && location.services.length > 0) {
        let servicesHtml = '';
        location.services.forEach(service => {
            servicesHtml += `<span class="badge bg-light text-dark border">${service}</span>`;
        });
        servicesDiv.innerHTML = servicesHtml;
        servicesSection.style.display = 'block';
    } else {
        servicesSection.style.display = 'none';
    }
    
    // Update additional info
    const additionalInfo = document.getElementById('additional-info');
    const managerSection = document.getElementById('manager-section');
    const establishedSection = document.getElementById('established-section');
    
    let hasAdditionalInfo = false;
    
    if (location.manager) {
        document.getElementById('selected-location-manager').textContent = location.manager;
        managerSection.style.display = 'block';
        hasAdditionalInfo = true;
    } else {
        managerSection.style.display = 'none';
    }
    
    if (location.established) {
        document.getElementById('selected-location-established').textContent = location.established;
        establishedSection.style.display = 'block';
        hasAdditionalInfo = true;
    } else {
        establishedSection.style.display = 'none';
    }
    
    if (hasAdditionalInfo) {
        additionalInfo.style.display = 'block';
    } else {
        additionalInfo.style.display = 'none';
    }
};

// Initialize the component
document.addEventListener('DOMContentLoaded', function() {
    // Show no selection message initially
    const noSelectionMessage = document.getElementById('no-selection-message');
    const selectedLocationDisplay = document.getElementById('selected-location-display');
    
    if (noSelectionMessage && selectedLocationDisplay) {
        noSelectionMessage.style.display = 'block';
        selectedLocationDisplay.style.display = 'none';
    }
});
