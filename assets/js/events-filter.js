// assets/js/events-filter.js

document.addEventListener('DOMContentLoaded', function () {
    const urlPath = 'http://localhost/techmedia'; // Adjust this to your base URL
    const filterForm = document.getElementById('event-filter-form');
    const eventsContainer = document.getElementById('events-list');
    const paginationContainer = document.getElementById('events-pagination');

    function showSkeletonLoader(count = 6) {
        eventsContainer.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const skeleton = document.createElement('div');
            skeleton.className = 'col-lg-4 col-md-6';
            skeleton.innerHTML = `
                <div class="event-card skeleton-loader">
                    <div class="event-img skeleton-logo"></div>
                    <div class="event-body">
                        <div class="skeleton-line skeleton-title mb-2"></div>
                        <div class="skeleton-line skeleton-text mb-2"></div>
                        <div class="skeleton-line skeleton-badge mb-2"></div>
                        <div class="skeleton-line skeleton-text mb-2"></div>
                    </div>
                </div>
            `;
            eventsContainer.appendChild(skeleton);
        }
    }

    function fetchEvents(page = 1) {
        showSkeletonLoader();
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        params.append('page', page);
        fetch('../api/events.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
                renderEvents(data.events);
                renderPagination(data.page, data.total_pages);
            });
    }

    function renderEvents(events) {
        eventsContainer.innerHTML = '';
        if (!events.length) {
            eventsContainer.innerHTML = '<div class="col-12 text-center py-5"><h4 class="text-muted">No events found matching your criteria.</h4></div>';
            return;
        }
        events.forEach(event => {
            const div = document.createElement('div');
            div.className = 'col-lg-4 col-md-6';
            div.innerHTML = `
                <div class="event-card">
                    <img src="${event.banner_image_url ? urlPath +'/'+ event.banner_image_url : 'https://placehold.coLS/600x400/f1f5f9/2563eb?text=Tech+Event'}" class="event-img" alt="Event">
                    <div class="event-body">
                        <span class="event-category">${event.event_type ? event.event_type.charAt(0).toUpperCase() + event.event_type.slice(1) : ''}</span>
                        <h4 class="fw-bold mb-2">${event.title}</h4>
                        <p class="text-muted small mb-3">${event.description ? event.description.substring(0, 80) : ''}...</p>
                        <div class="event-meta">
                            <span><i class="fas fa-calendar me-1"></i> ${event.start_datetime ? new Date(event.start_datetime).toLocaleDateString() : ''}</span>
                            <span><i class="fas fa-map-marker-alt me-1"></i> ${event.is_virtual == 1 ? 'Online' : (event.city_name || 'Nigeria')}</span>
                        </div>
                        <a href="${event.is_featured == 1 ? 'featured-event' : 'event'}/${event.slug}" class="btn btn-outline-primary btn-sm mt-3">View Details</a>
                    </div>
                </div>
            `;
            eventsContainer.appendChild(div);
        });
    }

    function renderPagination(current, total) {
        paginationContainer.innerHTML = '';
        if (total <= 1) return;
        for (let i = 1; i <= total; i++) {
            const btn = document.createElement('a');
            btn.className = 'btn btn-sm m-1 ' + (i === current ? 'btn-primary' : 'btn-outline-primary');
            btn.textContent = i;
            btn.href = '#events-list-filter';
            btn.onclick = (e) => { e.preventDefault(); fetchEvents(i); };
            paginationContainer.appendChild(btn);
        }
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchEvents(1);
        });
    }

    // Initial load
    fetchEvents(1);
});
