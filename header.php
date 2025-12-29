    <!-- Modern Header -->
    <div class="navbar-container" >
        <div class="container">
            <nav class="navbar navbar-expand-lg"style="height: 6em;">
                <!-- <i class="bi bi-hexagon-fill text-primary-accent"></i>
                TECH<span>MEDIA</span> -->
                <div class="col-2 d-flex align-items-center">
                    <a class="" href="#">
                        <img src="<?= $url_path ?>/assets/images/techstream-logo.png" class="img-fluid" alt="TechMedia Logo">
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="collapse navbar-collapse desktop-nav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item"><a class="nav-link" id="home" href="<?= htmlspecialchars($url_path) ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" id="about-us" href="<?= htmlspecialchars($url_path) ?>/about-us">About</a></li>

                        <!-- States Dropdown (Full Width) -->
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle" id="states" href="#" data-bs-toggle="dropdown">States</a>
                            <div class="dropdown-menu" style="width:100%;min-width:600px;">
                                <div class="container">
                                    <div class="row p-3">
                                        <?php
                                        $states = $pdo->query("SELECT s.*, COUNT(c.id) as company_count FROM states s LEFT JOIN companies c ON s.id = c.state_id GROUP BY s.id ORDER BY company_count DESC, s.name ASC LIMIT 20")->fetchAll();
                                        ?>
                                        <div class="row col-md-12">
                                            <?php foreach ($states as $state): ?>
                                                <div class="col-md-3 mb-3">
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="<?= $url_path ?>/states?state_id=<?= $state['id'] ?>">
                                                        <span><?= htmlspecialchars($state['name']) ?></span>
                                                        <span class="badge bg-light text-dark ms-2"><?= $state['company_count'] ?></span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="col-md-3 mb-3">
                                                <a class="dropdown-item btn btn-outline-primary w-100 fw-bold" href="<?= $url_path ?>/all-states">View All States</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- Categories Dropdown (Full Width) -->
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle" id="categories" href="#" data-bs-toggle="dropdown">Services</a>
                            <div class="dropdown-menu">
                                <div class="container">
                                    <div class="row p-3">
                                        <div class="row col-md-12">
                                            <?php
                                            $parent_categories = $pdo->query("SELECT * FROM categories WHERE parent_id=0 AND type='company' ORDER BY name ASC LIMIT 6")->fetchAll();
                                            foreach ($parent_categories as $parent) {
                                                $child_categories = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC LIMIT 4");
                                                $child_categories->execute([$parent['id']]);
                                                $child_categories = $child_categories->fetchAll();
                                            ?>
                                                <div class="col-md-3 mb-4">
                                                    <h6 class="text-primary-accent mb-1 text-uppercase fw-bold"><i class="<?php echo $parent['icon_class']; ?> me-2"></i><?php echo htmlspecialchars($parent['name']); ?></h6>
                                                    <?php foreach ($child_categories as $child) { ?>
                                                        <a class="dropdown-item" href="<?= htmlspecialchars($url_path) ?>/categories/<?php echo $child['slug']; ?>"><?php echo htmlspecialchars($child['name']); ?></a>
                                                    <?php } ?>
                                                </div>

                                            <?php } ?>
                                        </div>
                                        <!-- <div class="col-md-2 border-start border-secondary d-none d-md-block">
                                            <div class="text-center">
                                                <i class="fas fa-rocket fa-3x mb-3 text-secondary"></i>
                                                <p class="small text-muted">Explore over 50+ categories in the Nigerian Tech Space.</p>
                                                <a href="all-categories" class="btn btn-outline-primary btn-sm">View All</a>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Events Dropdown (Full Width) -->
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown">Events</a>
                            <div class="dropdown-menu">
                                <div class="container">
                                    <div class="row p-4">

                                        <div class="row col-md-10">
                                            <?php
                                            $parent_categories = $pdo->query("SELECT * FROM categories WHERE parent_id=0 AND type='event' ORDER BY name ASC LIMIT 6")->fetchAll();
                                            foreach ($parent_categories as $parent) {
                                                $child_categories = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC LIMIT 4");
                                                $child_categories->execute([$parent['id']]);
                                                $child_categories = $child_categories->fetchAll();
                                            ?>
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-primary-accent mb-1 text-uppercase fw-bold"><i class="<?php echo $parent['icon_class']; ?> me-2"></i><?php echo htmlspecialchars($parent['name']); ?></h6>
                                                    <?php foreach ($child_categories as $child) { ?>
                                                        <a class="dropdown-item" href="<?= $url_path ?>/events/<?php echo $child['slug']; ?>"><?php echo htmlspecialchars($child['name']); ?></a>
                                                    <?php } ?>
                                                </div>

                                            <?php } ?>
                                        </div>
                                        <div class="col-md-2 border-start border-secondary d-none d-md-block">
                                            <div class="text-center p-2">
                                                <i class="fas fa-users fa-3x mb-3 text-secondary"></i>
                                                <p class="small text-muted">Find networking opportunities and industry events.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item"><a class="nav-link" id="news" href="<?= $url_path ?>/news">News</a></li>
                        <li class="nav-item"><a class="nav-link" id="news" href="<?= $url_path ?>/jobs">Jobs</a></li>
                    </ul>
                    <div class="d-flex mt-3 mt-lg-0">
                        <a href="<?= $url_path ?>/get-listed"><button class="btn-get-listed">Get Listed</button></a>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle navbar-toggler border-0" type="button">
                    <i class="fas fa-bars text-primary-accent" style="font-size: 1.5rem;"></i>
                </button>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu-container" id="mobileMenu">
        <div class="mobile-menu-header">
            <a class="navbar-brand" href="#">
                <i class="bi bi-hexagon-fill text-primary-accent"></i>
                TECH<span>MEDIA</span>
            </a>
            <button class="mobile-menu-close border-0 bg-transparent">
                <i class="fas fa-times text-primary-accent" style="font-size: 1.5rem;"></i>
            </button>
        </div>

        <a href="index" id="home-responsive" class="mobile-nav-link">Home</a>
        <a href="about-us" id="about-us-responsive" class="mobile-nav-link">About</a>

        <div class="mobile-dropdown">
            <a href="#" id="categories-mobile" class="mobile-nav-link d-flex justify-content-between align-items-center">
                Browse Companies
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="mobile-dropdown-content">
                <a href="categories" class="mobile-nav-link">Web Development</a>
                <a href="#" class="mobile-nav-link">Mobile Apps</a>
                <a href="#" class="mobile-nav-link">SaaS Platforms</a>
                <a href="#" class="mobile-nav-link">Robotics</a>
                <a href="#" class="mobile-nav-link">Smart Home</a>
                <a href="#" class="mobile-nav-link">Networking</a>
                <a href="#" class="mobile-nav-link">Machine Learning</a>
                <a href="#" class="mobile-nav-link">Data Analytics</a>
                <a href="#" class="mobile-nav-link">Cloud Computing</a>
            </div>
        </div>

        <div class="mobile-dropdown">
            <a href="#" id="events-mobile" class="mobile-nav-link d-flex justify-content-between align-items-center">
                Events
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="mobile-dropdown-content">
                <a href="#" class="mobile-nav-link">Conferences</a>
                <a href="#" class="mobile-nav-link">Hackathons</a>
                <a href="#" class="mobile-nav-link">Workshops</a>
                <a href="#" class="mobile-nav-link">Webinars</a>
            </div>
        </div>

        <a href="news" id="news-mobile" class="mobile-nav-link">News</a>
        <a href="news" id="news-mobile" class="mobile-nav-link">News</a>

        <div class="mt-4 pt-4 border-top border-secondary">
            <button class="btn-get-listed w-100">Get Listed</button>
        </div>
    </div> 