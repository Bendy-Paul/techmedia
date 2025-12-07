<?php
require 'includes/wertyuytre.php';

// 1. Get Slug & Validate
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: 404.html");
    exit();
}

$slug = $_GET['slug'];

// 2. Fetch Event Data
$stmt = $pdo->prepare("
    SELECT e.*, 
           c.name as city_name,
           comp.name as organizer_name,
           comp.slug as organizer_slug,
           comp.logo_url as organizer_logo
    FROM events e
    LEFT JOIN cities c ON e.city_id = c.id
    LEFT JOIN companies comp ON e.organizer_company_id = comp.id
    WHERE e.slug = ?
");
$stmt->execute([$slug]);
$event = $stmt->fetch();

// 3. Validate Existence
if (!$event) {
    header("Location: 404.html");
    exit();
}

// 4. Redirect DOWNGRADE if NOT Featured
if ($event['is_featured'] == 0) {
    header("Location: event.php?slug=" . urlencode($slug));
    exit();
}

// 5. Fetch Related Data
// Speakers
$spkStmt = $pdo->prepare("SELECT * FROM event_speakers WHERE event_id = ?");
$spkStmt->execute([$event['id']]);
$speakers = $spkStmt->fetchAll();

// Gallery
$galStmt = $pdo->prepare("SELECT * FROM event_galleries WHERE event_id = ?");
$galStmt->execute([$event['id']]);
$gallery = $galStmt->fetchAll();

// Parse JSONs
$socials = json_decode($event['social_links'] ?? '{}', true);
$community = json_decode($event['community_links'] ?? '{}', true);

// Calculate Duration (Approx)
$start = new DateTime($event['start_datetime']);
$end = new DateTime($event['end_datetime']);
$duration = $start->diff($end);
$duration_str = $duration->days > 0 ? $duration->days . " Days" : $duration->h . " Hours";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?> | Featured Event | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>
    <style>
        /* Event Hero */
        .event-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 120px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .event-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }

        .community-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: rgba(37, 99, 235, 0.05);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-main);
            transition: 0.3s;
            margin-bottom: 10px;
        }
        .community-link:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
        }

        .event-hero-content {
            position: relative;
            z-index: 2;
        }
        .featured-event-badge {
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

        /* Info Cards */
        .info-card {
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            padding: 25px;
            transition: transform 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-5px);
        }
        .ticket-card {
            background: linear-gradient(135deg, var(--primary-accent), var(--secondary-accent));
            color: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
        }
        .organizer-card {
            text-align: center;
            padding: 20px;
        }
        .organizer-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-accent);
            margin: 0 auto 15px;
            overflow: hidden;
        }
        .organizer-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }

        /* Media Elements */
        .video-container {
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        .gallery-item {
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 3rem;
        }
        .gallery-item img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-accent);
            text-decoration: none;
            transition: 0.3s;
        }
        .social-icon:hover {
            background: var(--primary-accent);
            color: white;
        }

        @media (max-width: 768px) {
            .event-hero {
                padding: 100px 0 40px;
            }
        }
    </style>
</head>
<body>

    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>
    <script>
        document.getElementById('events').classList.add('active');
        document.getElementById('events-mobile').classList.add('active');
    </script>
    
    <!-- Event Hero -->
    <section class="event-hero">
        <div class="container event-hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <span class="featured-event-badge"><i class="fas fa-crown"></i> FEATURED EVENT</span>
                    <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($event['title']) ?></h1>
                    <p class="lead mb-4"><?= htmlspecialchars($event['description']) ?></p> <!-- Assuming short description in DB or use substr -->
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <span><i class="fas fa-calendar me-2"></i> <?= date('M d-y', strtotime($event['start_datetime'])) ?></span>
                        <span><i class="fas fa-map-marker-alt me-2"></i> <?= $event['is_virtual'] ? 'Online' : htmlspecialchars($event['location_name']) ?></span>
                        <span><i class="fas fa-clock me-2"></i> <?= $duration_str ?></span>
                    </div>
                    <div class="d-flex gap-3">
                        <?php if($event['ticket_url']): ?>
                        <a href="<?= htmlspecialchars($event['ticket_url']) ?>" target="_blank" class="btn btn-light btn-lg">Get Tickets</a>
                        <?php endif; ?>
                        <!-- <a href="#" class="btn btn-outline-light btn-lg">Save to Calendar</a> -->
                    </div>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <div class="ticket-card">
                        <h3 class="fw-bold mb-3">Ticket Price</h3>
                        <h2 class="display-4 fw-bold mb-3"><?= $event['price'] > 0 ? '₦'.number_format($event['price']) : 'Free' ?></h2>
                        <p class="mb-4">Full access to all sessions and networking events.</p>
                        <?php if($event['ticket_url']): ?>
                        <a href="<?= htmlspecialchars($event['ticket_url']) ?>" target="_blank" class="btn btn-light btn-lg w-100">Buy Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Event Details -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">About the Summit</h3>
                    <div style="white-space: pre-line;">
                        <?= htmlspecialchars($event['description']) ?>
                    </div>
                </div>

                <!-- Speakers -->
                <?php if(count($speakers) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">Featured Speakers</h3>
                    <div class="row">
                        <?php foreach($speakers as $spk): ?>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="organizer-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <?php if($spk['image_url']): ?>
                                        <img src="<?= htmlspecialchars($spk['image_url']) ?>" alt="<?= htmlspecialchars($spk['name']) ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($spk['name']) ?></h5>
                                    <p class="text-muted small mb-1"><?= htmlspecialchars($spk['position']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Gallery -->
                <?php if(count($gallery) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Past Event Gallery</h3>
                    <div class="row g-3">
                        <?php foreach($gallery as $img): ?>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Gallery" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event Info -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Event Information</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Date & Time</h6>
                        <p class="mb-0">
                            <?= date('F d, Y', strtotime($event['start_datetime'])) ?><br>
                            <?= date('h:i A', strtotime($event['start_datetime'])) ?> - <?= date('h:i A', strtotime($event['end_datetime'])) ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Location</h6>
                        <p class="mb-0"><?= $event['is_virtual'] ? 'Online Event' : htmlspecialchars($event['location_name']) ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Event Type</h6>
                        <span class="badge bg-primary"><?= ucfirst($event['event_type']) ?></span>
                    </div>
                </div>
                
                <!-- Organizers -->
                <?php if($event['organizer_company_id']): ?>
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Event Organizers</h4>
                    <div class="organizer-card">
                        <div class="organizer-avatar">
                            <?php if(!empty($event['organizer_logo'])): ?>
                                <img src="<?= htmlspecialchars($url_path) ?>/<?= htmlspecialchars($event['organizer_logo']) ?>" alt="Organizer">
                            <?php else: ?>
                                <i class="fas fa-user-tie"></i>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($event['organizer_name']) ?></h5>
                        <a href="listed.php?slug=<?= htmlspecialchars($event['organizer_slug']) ?>" class="btn btn-outline-primary btn-sm mt-2">View Profile</a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Community Links (Dynamic) -->
                <?php if(!empty($community)): ?>
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Join Our Community</h4>
                    <?php if(!empty($community['whatsapp'])): ?>
                    <a href="<?= htmlspecialchars($community['whatsapp']) ?>" class="community-link" target="_blank">
                        <i class="fab fa-whatsapp text-success"></i>
                        <div><h6 class="fw-bold mb-1">WhatsApp Group</h6></div>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($community['telegram'])): ?>
                    <a href="<?= htmlspecialchars($community['telegram']) ?>" class="community-link" target="_blank">
                        <i class="fab fa-telegram text-primary"></i>
                        <div><h6 class="fw-bold mb-1">Telegram Channel</h6></div>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($community['newsletter'])): ?>
                    <a href="<?= htmlspecialchars($community['newsletter']) ?>" class="community-link" target="_blank">
                        <i class="fas fa-envelope text-danger"></i>
                        <div><h6 class="fw-bold mb-1">Newsletter</h6></div>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Social Media -->
                <?php if(!empty($socials)): ?>
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Follow the Event</h4>
                    <div class="d-flex gap-3 justify-content-center">
                        <?php if(!empty($socials['twitter'])): ?><a href="<?= $socials['twitter'] ?>" class="social-icon"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if(!empty($socials['linkedin'])): ?><a href="<?= $socials['linkedin'] ?>" class="social-icon"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                        <?php if(!empty($socials['instagram'])): ?><a href="<?= $socials['instagram'] ?>" class="social-icon"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if(!empty($socials['facebook'])): ?><a href="<?= $socials['facebook'] ?>" class="social-icon"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Location Map -->
                <?php if(!$event['is_virtual']): ?>
                <div class="info-card" data-aos="fade-left" data-aos-delay="300">
                    <h4 class="fw-bold mb-4">Location Map</h4>
                    <div class="bg-light rounded-3 p-3 mb-3 text-center">
                        <i class="fas fa-map-marked-alt text-primary-accent" style="font-size: 3rem;"></i>
                        <p class="mt-2 mb-0"><?= htmlspecialchars($event['location_name']) ?></p>
                    </div>
                    <a href="https://maps.google.com/?q=<?= urlencode($event['location_name'] . ' ' . $event['city_name']) ?>" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View on Map
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include_once('footer.php'); ?>

</body>
</html>