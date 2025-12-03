<aside class="sidebar d-flex flex-column" id="sidebar">
    <!-- Logo Area -->
    <div class="p-4 border-bottom">
        <a href="index.php" class="text-decoration-none d-flex align-items-center gap-2">
            <i class="fas fa-hexagon text-primary fa-lg"></i>
            <span class="fw-bold text-dark h5 mb-0" style="font-family: 'Space Grotesk'">TECH<span class="text-primary">MEDIA</span></span>
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex-grow-1 overflow-auto p-3 custom-scrollbar">
        
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <div class="section-title">Directory</div>
        
        <a href="companies.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
            <i class="fas fa-building"></i> Companies
        </a>
        <a href="verification-requests.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'verification-requests.php' ? 'active' : ''; ?>">
            <i class="fas fa-check-circle"></i> Verification <span class="badge bg-danger rounded-pill ms-auto">3</span>
        </a>
        <a href="reviews.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> Reviews
        </a>

        <div class="section-title">Content</div>
        
        <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> News / Blog
        </a>
        <a href="events.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Events
        </a>

        <div class="section-title">Settings</div>
        
        <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="locations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i> Locations
        </a>
        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
    </div>

    <!-- User Profile Snippet at Bottom -->
    <div class="p-3 border-top bg-light">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-user"></i>
                </div>
                <div style="line-height: 1.2;">
                    <small class="d-block fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
                    <small class="text-muted" style="font-size: 0.75rem;">Super Admin</small>
                </div>
            </div>
            <a href="logout.php" class="text-danger" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</aside>