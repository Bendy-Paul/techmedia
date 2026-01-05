// assets/js/states-companies.js

document.addEventListener('DOMContentLoaded', function () {
    let url_path = window.TECHMEDIA_URL_PATH || '';
    // Add smooth scrolling to states
    document.querySelectorAll('.state-chip').forEach(chip => {
        chip.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });

    // Pagination state
    let currentPage = 1;
    let totalPages = 1;

    // Fetch and render companies
    async function fetchCompanies(page = 1) {
        const grid = document.getElementById('companies-grid');
        const count = document.getElementById('companies-count');
        const stateSlug = document.body.getAttribute('data-state-slug') || '';
        const stateId = document.body.getAttribute('data-state-id') || '';
        const stateName = document.body.getAttribute('data-state-name') || '';
        const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
        const params = new URLSearchParams();
        params.append('slug', stateSlug);
        params.append('location', stateId);
        selectedCategories.forEach(cat => params.append('categories[]', cat));
        params.append('per_page', 12);
        params.append('page', page);
        try {
            const response = await fetch(url_path + '/api/companies.php?' + params.toString());
            const data = await response.json();
            count.textContent = `${data.total} compan${data.total === 1 ? 'y' : 'ies'}`;
            totalPages = data.total_pages || 1;
            currentPage = data.page || 1;
            let hasCompanies = data.companies.length > 0;
            if (hasCompanies) {
                let html = '';
                data.companies.forEach(company => {
                    html += `
                    <div class="company-card-modern">
                        <div class="card-header">
                            <div class="company-logo">
                                ${company.logo_url ? `<img src="${url_path}/${company.logo_url}" alt="${company.name}">` : `<i class='fas fa-building placeholder'></i>`}
                            </div>
                            <div class="company-info">
                                <h3 class="company-name">${company.name}</h3>
                                ${company.tagline ? `<p class="company-tagline">${company.tagline}</p>` : ''}
                                <div class="company-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    ${stateName}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="${url_path}/listed/${company.slug}" class="view-profile-btn">
                                View Company Profile <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>`;
                });
                grid.innerHTML = html;
            } else {
                grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No companies found</h3>
                    <p>Try selecting different categories or browse another state to discover more companies.</p>
                    <a href="${url_path}/states/${stateSlug}" class="view-profile-btn" style="max-width: 200px; margin: 0 auto;">
                        Reset Filters
                    </a>
                </div>`;
            }
            renderPagination(hasCompanies);
        } catch (error) {
            grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Error loading companies</h3>
                <p>Please try again later.</p>
            </div>`;
            renderPagination(false);
        }
    }

    function renderPagination(hasCompanies) {
        const pagContainer = document.getElementById('companies-pagination');
        if (!pagContainer) return;
        if (!hasCompanies || totalPages <= 1) {
            pagContainer.innerHTML = '';
            return;
        }
        let html = '<nav class="pagination-nav m-auto text-center"><ul class="pagination">';
        // Previous button
        html += `<li class="page-item${currentPage === 1 ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`;

        let startPage = 1;
        let endPage = totalPages;
        if (totalPages > 7) {
            if (currentPage <= 4) {
                startPage = 1;
                endPage = 5;
            } else if (currentPage >= totalPages - 3) {
                startPage = totalPages - 4;
                endPage = totalPages;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }

        // First page
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item${i === currentPage ? ' active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        html += `<li class="page-item${currentPage === totalPages ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`;

        html += '</ul></nav>';
        pagContainer.innerHTML = html;
        pagContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage && page >= 1 && page <= totalPages) fetchCompanies(page);
            });
        });
    }

    // Update companies when filters change
    document.querySelectorAll('.category-checkbox').forEach(cb => {
        cb.addEventListener('change', () => fetchCompanies(1));
    });

    // Expose for manual reload
    window.fetchCompanies = fetchCompanies;

    // Initial fetch
    if (document.getElementById('companies-grid')) fetchCompanies(1);
});
