<!-- search-result.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | Tech Media Directory</title>
    
    <?php include_once('links.php')?>

    <style>
        .search-header {
            background: #f8fafc;
            padding: 1rem 0;
        }
        .search-filters {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .result-card {
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .result-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .type-company {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        .type-event {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }
        .type-article {
            background: rgba(168, 85, 247, 0.1);
            color: #7c3aed;
        }
        .result-image {
            width: 80px;
            height: 80px;
            border-radius: 0.75rem;
            object-fit: cover;
        }
        .pagination .page-link {
            border: none;
            color: var(--text-muted);
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-accent);
            color: white;
        }
        .result-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .result-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

        <?php include_once('header.php') ?>

    <!-- Search Header -->
    <section class="search-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">Search Results</h1>
                    
                    <!-- Search Form -->
                    <form class="search-container mx-auto">
                        <div class="d-flex align-items-center flex-grow-1 border-end border-secondary border-opacity-25">
                            <i class="fas fa-search ms-3 text-muted"></i>
                            <input type="text" class="search-input" placeholder="Search companies or events..." value="Fintech">
                        </div>
                        <button type="button" class="search-btn"><i class="fas fa-arrow-right"></i></button>
                    </form>
                    
                    <p class="text-muted mt-3 mb-0">Showing results for "Fintech"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="mt-5">
        <div class="container">
            <div class="row">
                <!-- Filters -->
                <div class="col-lg-3 mb-4">
                    <div class="search-filters">
                        <h5 class="fw-bold mb-3">Filters</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter-company" checked>
                                <label class="form-check-label" for="filter-company">Companies</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter-event" checked>
                                <label class="form-check-label" for="filter-event">Events</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter-article" checked>
                                <label class="form-check-label" for="filter-article">Articles</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Location</label>
                            <select class="form-select">
                                <option value="">All Locations</option>
                                <option value="lagos">Lagos</option>
                                <option value="abuja">Abuja</option>
                                <option value="port-harcourt">Port Harcourt</option>
                                <option value="ibadan">Ibadan</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select">
                                <option value="">All Categories</option>
                                <option value="fintech">Fintech</option>
                                <option value="healthtech">HealthTech</option>
                                <option value="edutech">EduTech</option>
                                <option value="agritech">AgriTech</option>
                                <option value="ecommerce">E-commerce</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
                
                <!-- Results -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="text-muted mb-0">24 results found</p>
                        <div>
                            <label class="form-label me-2">Sort by:</label>
                            <select class="form-select d-inline-block w-auto">
                                <option value="relevance">Relevance</option>
                                <option value="newest">Newest</option>
                                <option value="popular">Most Popular</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Company Result -->
                    <div class="result-card">
                        <span class="result-type type-company">Company</span>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Paystack" class="result-image" alt="Company">
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-2">Paystack</h4>
                                <p class="text-muted mb-2">Modern payments and financial infrastructure for African businesses. We make it easy for businesses to accept payments from anyone, anywhere in the world.</p>
                                <div class="result-meta">
                                    <div class="result-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Lagos, Nigeria</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>200+ employees</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Fintech, Payments</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Event Result -->
                    <div class="result-card">
                        <span class="result-type type-event">Event</span>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Fintech+Summit" class="result-image" alt="Event">
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-2">Nigeria Fintech Summit 2025</h4>
                                <p class="text-muted mb-2">Annual gathering of fintech leaders, regulators, and innovators to discuss the future of financial technology in Nigeria and across Africa.</p>
                                <div class="result-meta">
                                    <div class="result-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>March 15-16, 2025</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Landmark Centre, Lagos</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>1,200+ attendees</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-outline-primary">View Event</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article Result -->
                    <div class="result-card">
                        <span class="result-type type-article">Article</span>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Fintech+Trends" class="result-image" alt="Article">
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-2">The Future of Nigerian Fintech: 5 Trends to Watch</h4>
                                <p class="text-muted mb-2">An in-depth analysis of emerging trends in Nigeria's fintech sector, from embedded finance to regulatory changes and investment patterns.</p>
                                <div class="result-meta">
                                    <div class="result-meta-item">
                                        <i class="fas fa-user-edit"></i>
                                        <span>By Tolu Adeleke</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>January 10, 2025</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>8 min read</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-outline-primary">Read Article</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Company Result -->
                    <div class="result-card">
                        <span class="result-type type-company">Company</span>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Flutterwave" class="result-image" alt="Company">
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-2">Flutterwave</h4>
                                <p class="text-muted mb-2">Building payments infrastructure to connect Africa to the global economy. Our APIs help businesses across Africa get paid by anyone, anywhere.</p>
                                <div class="result-meta">
                                    <div class="result-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Lagos, Nigeria</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>500+ employees</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Fintech, Payments</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Event Result -->
                    <div class="result-card">
                        <span class="result-type type-event">Event</span>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Blockchain+Event" class="result-image" alt="Event">
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-2">Blockchain & Digital Assets Conference</h4>
                                <p class="text-muted mb-2">Exploring the intersection of blockchain technology, digital assets, and financial services in the Nigerian market.</p>
                                <div class="result-meta">
                                    <div class="result-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>April 5, 2025</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Eko Hotel, Lagos</span>
                                    </div>
                                    <div class="result-meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>800+ attendees</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-outline-primary">View Event</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Search results pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>

<?php include_once('footer.php')?>
</body>
</html>