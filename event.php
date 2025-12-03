<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lagos AI & Machine Learning Meetup | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

    <style>
        /* Event Hero */
        .event-hero {
            background: linear-gradient(135deg, #b3d0f6ff 0%, #f8fafc 100%);
            padding: 100px 0 40px;
            position: relative;
        }
        .event-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: 
                radial-gradient(circle at 15% 30%, rgba(37, 99, 235, 0.1) 0%, transparent 45%),
                radial-gradient(circle at 85% 70%, rgba(59, 130, 246, 0.05) 0%, transparent 45%);
            z-index: 1;
        }
        .event-hero-content {
            position: relative;
            z-index: 2;
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
            background: rgba(37, 99, 235, 0.05);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            border: 1px solid rgba(37, 99, 235, 0.1);
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
        }

        /* Media Elements */
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

        @media (max-width: 768px) {
            .event-hero {
                padding: 80px 0 30px;
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
                    <span class="badge bg-primary mb-3">Meetup</span>
                    <h1 class="display-4 fw-bold mb-3">Lagos AI & Machine Learning Meetup</h1>
                    <p class="lead text-muted mb-4">Monthly gathering for AI enthusiasts, researchers, and practitioners to share knowledge and network.</p>
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <span><i class="fas fa-calendar me-2 text-primary-accent"></i> April 20, 2025</span>
                        <span><i class="fas fa-map-marker-alt me-2 text-primary-accent"></i> Co-Creation Hub, Yaba, Lagos</span>
                        <span><i class="fas fa-users me-2 text-primary-accent"></i> 150+ Expected Participants</span>
                        <span><i class="fas fa-clock me-2 text-primary-accent"></i> 3 Hours</span>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-primary btn-lg">RSVP Now</a>
                        <a href="#" class="btn btn-outline-primary btn-lg">Save to Calendar</a>
                    </div>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <div class="ticket-card">
                        <h3 class="fw-bold mb-3">Free Admission</h3>
                        <p class="mb-4">This event is free to attend. RSVP required due to limited space.</p>
                        <a href="#" class="btn btn-primary btn-lg w-100">RSVP Now</a>
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
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Event Details</h3>
                    <p class="mb-3">Join us for our monthly AI & Machine Learning Meetup where we explore the latest developments in artificial intelligence and its practical applications.</p>
                    <p class="mb-3">This month's theme is "AI in Healthcare: Transforming Diagnosis and Treatment." We'll have presentations from leading researchers and practitioners working at the intersection of AI and healthcare.</p>
                    <p class="mb-4">Whether you're a seasoned data scientist or just getting started with AI, this meetup offers valuable insights, networking opportunities, and hands-on learning experiences.</p>
                    
                    <h5 class="fw-bold mt-4 mb-3">Agenda</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex">
                                <span class="badge bg-primary me-3">6:00 PM</span>
                                <div>
                                    <h6 class="fw-bold mb-1">Registration & Networking</h6>
                                    <p class="text-muted small mb-0">Arrive, check in, and connect with fellow attendees</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <span class="badge bg-primary me-3">6:30 PM</span>
                                <div>
                                    <h6 class="fw-bold mb-1">Welcome & Introductions</h6>
                                    <p class="text-muted small mb-0">Meetup organizers and community updates</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <span class="badge bg-primary me-3">6:45 PM</span>
                                <div>
                                    <h6 class="fw-bold mb-1">Keynote: AI in Medical Imaging</h6>
                                    <p class="text-muted small mb-0">Dr. Adeola Johnson, Lead AI Researcher at MedTech Labs</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <span class="badge bg-primary me-3">7:30 PM</span>
                                <div>
                                    <h6 class="fw-bold mb-1">Lightning Talks</h6>
                                    <p class="text-muted small mb-0">Community presentations on AI projects</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-0">
                            <div class="d-flex">
                                <span class="badge bg-primary me-3">8:15 PM</span>
                                <div>
                                    <h6 class="fw-bold mb-1">Networking & Refreshments</h6>
                                    <p class="text-muted small mb-0">Continue conversations and connections</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>


<!-- 
                Speakers 
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">Featured Speakers</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="organizer-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-1">Dr. Adeola Johnson</h5>
                                    <p class="text-muted small mb-1">Lead AI Researcher, MedTech Labs</p>
                                    <p class="small text-muted mb-0">Expert in medical imaging and diagnostic AI systems</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="organizer-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-1">Chinedu Okoro</h5>
                                    <p class="text-muted small mb-1">Data Scientist, HealthTech NG</p>
                                    <p class="small text-muted mb-0">Building predictive models for patient outcomes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                Gallery 
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Past Event Gallery</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Social Media -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="300" style="position: sticky; top: 100px;">
                    <h4 class="fw-bold mb-4">Follow Us</h4>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Location Map -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="400">
                    <h4 class="fw-bold mb-4">Location Map</h4>
                    <div class="bg-light rounded-3 p-3 mb-3 text-center">
                        <i class="fas fa-map-marked-alt text-primary-accent" style="font-size: 3rem;"></i>
                        <p class="mt-2 mb-0">Co-Creation Hub, Yaba, Lagos</p>
                    </div>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View on Map
                    </a>
                </div>

                <!-- Event Info -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Event Information</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Date & Time</h6>
                        <p class="mb-0">April 20, 2025<br>6:00 PM - 9:00 PM</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Location</h6>
                        <p class="mb-0">Co-Creation Hub<br>294 Herbert Macaulay Way<br>Yaba, Lagos</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Expected Participants</h6>
                        <p class="mb-0">150+</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Contact Information</h6>
                        <p class="mb-0">hello@lagosaiml.com<br>+234 901 876 5432</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Event Categories</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge bg-primary">AI</span>
                            <span class="badge bg-primary">Machine Learning</span>
                            <span class="badge bg-primary">Healthcare</span>
                            <span class="badge bg-primary">Technology</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <h6 class="text-muted mb-1">Event Duration</h6>
                        <p class="mb-0">3 Hours</p>
                    </div>
                </div>
                
                <!-- Organizers -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Event Organizers</h4>
                    <div class="organizer-card">
                        <div class="organizer-avatar">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Lagos AI Community</h5>
                        <p class="text-muted small">Largest AI and machine learning community in Lagos</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">View Profile</a>
                    </div>
                </div>
                
                <!-- Community Links -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Join Our Community</h4>
                    <a href="#" class="community-link">
                        <i class="fab fa-whatsapp text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-1">WhatsApp Group</h6>
                            <p class="text-muted small mb-0">Join 500+ members for daily discussions</p>
                        </div>
                    </a>
                    <a href="#" class="community-link">
                        <i class="fab fa-telegram text-primary"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Telegram Channel</h6>
                            <p class="text-muted small mb-0">Get updates and event announcements</p>
                        </div>
                    </a>
                    <a href="#" class="community-link">
                        <i class="fas fa-envelope text-danger"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Newsletter</h6>
                            <p class="text-muted small mb-0">Monthly digest of AI news and events</p>
                        </div>
                    </a>
                </div>
                
            </div>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

</body>
</html>