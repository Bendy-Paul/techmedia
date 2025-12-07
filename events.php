<?php
require 'includes/wertyuytre.php';

// Filter Logic
$type = $_GET['type'] ?? 'all';
$search = $_GET['s'] ?? '';

$sql = "SELECT e.*, c.name as city_name 
        FROM events e 
        LEFT JOIN cities c ON e.city_id = c.id 
        WHERE e.start_datetime >= NOW()";

if ($type != 'all') {
    $sql .= " AND e.event_type = :type";
}
if (!empty($search)) {
    $sql .= " AND e.title LIKE :search";
}

$sql .= " ORDER BY e.start_datetime ASC";

$stmt = $pdo->prepare($sql);
if ($type != 'all') $stmt->bindValue(':type', $type);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
$stmt->execute();
$events = $stmt->fetchAll();

// Fetch one featured event for the banner
$featStmt = $pdo->query("SELECT * FROM events WHERE is_featured = 1 AND start_datetime >= NOW() ORDER BY start_datetime ASC LIMIT 1");
$featuredEvent = $featStmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | Tech Media Directory</title>
    <?php include_once('links.php'); ?>
    <style>
        /* Events Hero */
        .events-hero {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.5) 0%, rgba(59, 130, 246, 0.45) 100%), url('../assets/images/event-hero.jpg') no-repeat center center/cover;
            padding: 120px 0 60px;
            color: white;
            position: relative;
        }

        .events-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 15% 30%, rgba(37, 99, 235, 0.1) 0%, transparent 45%),
                radial-gradient(circle at 85% 70%, rgba(59, 130, 246, 0.05) 0%, transparent 45%);
            z-index: 1;
        }

        .events-hero-content {
            position: relative;
            z-index: 2;
        }

        /* Featured Event Banner */
        .featured-event-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            margin-bottom: 50px;
        }

        .featured-badge {
            background: var(--featured-gold);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
        }

        /* Event Cards */
        .event-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            transition: 0.3s;
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .event-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .event-body {
            padding: 25px;
        }

        .event-category {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 12px;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Nigeria Map */
        .nigeria-map-container {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin: 50px 0;
        }

        .nigeria-svg {
            width: 100%;
            height: auto;
            max-height: 500px;
        }

        .state-path {
            fill: #cbd5e1;
            stroke: #94a3b8;
            stroke-width: 1;
            transition: 0.4s;
            cursor: pointer;
        }

        .state-path:hover {
            fill: #94a3b8;
            stroke: var(--primary-accent);
        }

        .state-hotspot-high {
            fill: rgba(37, 99, 235, 0.7) !important;
            stroke: var(--primary-accent) !important;
        }

        .state-hotspot-med {
            fill: rgba(37, 99, 235, 0.3) !important;
        }

        .map-tooltip {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--primary-accent);
            color: var(--text-main);
            padding: 10px 15px;
            border-radius: 8px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 100;
            font-size: 0.9rem;
            box-shadow: var(--shadow-medium);
        }

        /* Filter Section */
        .filter-section {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin-bottom: 30px;
        }

        .filter-btn {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            margin: 5px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-accent);
            color: white;
        }

        /* Footer */
        footer {
            background: #f1f5f9;
            border-top: 1px solid rgba(37, 99, 235, 0.1);
            padding: 80px 0 30px;
        }

        .footer-link {
            display: block;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .footer-link:hover {
            color: var(--primary-accent);
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            text-decoration: none;
            transition: 0.3s;
        }

        .social-btn:hover {
            background: var(--primary-accent);
            color: #fff;
        }

        @media (max-width: 768px) {

            .events-hero,
            .event-hero {
                padding: 100px 0 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>
    <script>
        document.getElementById('events').classList.add('active');
        document.getElementById('events-mobile').classList.add('active');
    </script>

    <!-- Events Hero -->
    <section class="events-hero">
        <div class="container events-hero-content">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h1 class="display-4 fw-bold mb-4">Tech Events in Nigeria</h1>
                    <p class="lead mb-4">Discover conferences, meetups, hackathons, and workshops across Nigeria's tech ecosystem.</p>
                    <div class="input-group input-group-lg mx-auto" style="max-width: 500px;">
                        <input type="text" class="form-control" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>" onkeypress="if(event.key === 'Enter') window.location.href='?s='+this.value">
                        <button class="btn btn-primary" type="button" onclick="window.location.href='?s='+this.previousElementSibling.value"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <!-- Featured Event (Dynamic) -->
        <?php if ($featuredEvent): ?>
        <div class="featured-event-banner" data-aos="fade-up">
            <div class="row align-items-center">
                <div class="col-lg-6 p-5">
                    <span class="featured-badge"><i class="fas fa-crown"></i> FEATURED EVENT</span>
                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($featuredEvent['title']) ?></h2>
                    <p class="mb-4"><?= htmlspecialchars(substr(strip_tags($featuredEvent['description']), 0, 150)) ?>...</p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <span><i class="fas fa-calendar me-2"></i> <?= date('M d-y', strtotime($featuredEvent['start_datetime'])) ?></span>
                        <span><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($featuredEvent['location_name']) ?></span>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="<?= htmlspecialchars($url_path) ?>/featured-event/<?= htmlspecialchars($featuredEvent['slug']) ?>" class="btn btn-light btn-lg">View Details</a>
                        <?php if($featuredEvent['ticket_url']): ?>
                        <a href="<?= htmlspecialchars($featuredEvent['ticket_url']) ?>" class="btn btn-outline-light btn-lg" target="_blank">Get Tickets</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 p-0">
                    <img src="<?= htmlspecialchars($featuredEvent['banner_image_url'] ?? 'https://placehold.co/600x400/f1f5f9/2563eb?text=Featured+Event') ?>" class="img-fluid w-100" alt="Featured Event" style="height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-section" data-aos="fade-up" data-aos-delay="100">
            <h5 class="fw-bold mb-3">Filter Events</h5>
            <div class="d-flex flex-wrap">
                <a href="events.php" class="filter-btn <?= $type == 'all' ? 'active' : '' ?>">All Events</a>
                <a href="events.php?type=conference" class="filter-btn <?= $type == 'conference' ? 'active' : '' ?>">Conferences</a>
                <a href="events.php?type=hackathon" class="filter-btn <?= $type == 'hackathon' ? 'active' : '' ?>">Hackathons</a>
                <a href="events.php?type=workshop" class="filter-btn <?= $type == 'workshop' ? 'active' : '' ?>">Workshops</a>
                <a href="events.php?type=meetup" class="filter-btn <?= $type == 'meetup' ? 'active' : '' ?>">Meetups</a>
                <a href="events.php?type=webinar" class="filter-btn <?= $type == 'webinar' ? 'active' : '' ?>">Webinars</a>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="row g-4">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="event-card">
                        <img src="<?= htmlspecialchars($event['banner_image_url'] ?? 'https://placehold.co/600x400/f1f5f9/2563eb?text=Tech+Event') ?>" class="event-img" alt="Event">
                        <div class="event-body">
                            <span class="event-category"><?= ucfirst($event['event_type']) ?></span>
                            <h4 class="fw-bold mb-2"><?= htmlspecialchars($event['title']) ?></h4>
                            <p class="text-muted small mb-3"><?= htmlspecialchars(substr(strip_tags($event['description']), 0, 80)) ?>...</p>
                            <div class="event-meta">
                                <span><i class="fas fa-calendar me-1"></i> <?= date('M d, Y', strtotime($event['start_datetime'])) ?></span>
                                <span><i class="fas fa-map-marker-alt me-1"></i> <?= $event['is_virtual'] ? 'Online' : htmlspecialchars($event['city_name'] ?? 'Nigeria') ?></span>
                            </div>
                            <!-- Link Logic: Redirect to featured-event.php if featured, else event.php -->
                            <?php $link = $event['is_featured'] ? 'featured-event' : 'event'; ?>
                            <a href="<?= $url_path ."/" . $link . "/" . $event['slug'] ?>" class="btn btn-outline-primary btn-sm mt-3">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">No events found matching your criteria.</h4>
                    <a href="events.php" class="btn btn-primary mt-3">View All Events</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Nigeria Events Heat Map (Static for now as per request to keep layout) -->
        <div class="nigeria-map-container" data-aos="fade-up" data-aos-delay="800">
            <h3 class="fw-bold mb-4 text-center">Tech Events Heat Map Across Nigeria</h3>
            <div class="position-relative">
                <div id="map-tooltip" class="map-tooltip"></div>
                <!-- SVG Code Omitted for Brevity - It remains the same as your file -->
                <!-- Use the SVG provided in your uploaded events.php file here -->
                <svg viewBox="0 0 800 600" class="nigeria-svg" id="nigeriaMap">
                    <!-- ... SVG Paths from your file ... -->
                    <path d="m 291.01165,491.65874..." class="state-path" title="Abia" id="NG-AB" />
                    <!-- (Rest of your SVG code) -->
                 </svg>

                <div class="text-center mt-3">
                    <small class="text-primary-accent"><i class="fas fa-circle"></i> High Event Density</small>
                    <small class="text-muted ms-3"><i class="fas fa-circle"></i> Medium Event Density</small>
                    <small class="text-muted ms-3"><i class="fas fa-circle"></i> Low Event Density</small>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
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

    <?php include_once('footer.php'); ?>

    <script>
        // Category filter functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Map Tooltip Logic
        document.querySelectorAll('.state-path').forEach(path => {
            path.addEventListener('mouseover', function(e) {
                const name = this.getAttribute('title'); // Changed to title as per SVG
                // You would typically fetch count dynamically here
                const count = Math.floor(Math.random() * 20) + " Events"; 
                const tooltip = document.getElementById('map-tooltip');

                tooltip.innerHTML = `<strong>${name}</strong><br><span class="text-primary-accent">${count}</span>`;
                tooltip.style.opacity = '1';
            });

            path.addEventListener('mouseout', function() {
                document.getElementById('map-tooltip').style.opacity = '0';
            });

            path.addEventListener('mousemove', function(e) {
                document.getElementById('map-tooltip').style.left = (e.pageX + 15) + 'px';
                document.getElementById('map-tooltip').style.top = (e.pageY + 15) + 'px';
            });
        });
    </script>
</body>

</html>