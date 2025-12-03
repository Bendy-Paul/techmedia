<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nigeria Tech Summit 2025 | Featured Event | Tech Media Directory</title>
    
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
                    <h1 class="display-4 fw-bold mb-3">Nigeria Tech Summit 2025</h1>
                    <p class="lead mb-4">Africa's largest gathering of tech innovators, investors, and policymakers. Join 5,000+ attendees for three days of networking, learning, and collaboration.</p>
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <span><i class="fas fa-calendar me-2"></i> Nov 15-17, 2025</span>
                        <span><i class="fas fa-map-marker-alt me-2"></i> Landmark Centre, Lagos</span>
                        <span><i class="fas fa-users me-2"></i> 5,000+ Expected Participants</span>
                        <span><i class="fas fa-clock me-2"></i> 3 Days</span>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-light btn-lg">Get Tickets</a>
                        <a href="#" class="btn btn-outline-light btn-lg">Save to Calendar</a>
                    </div>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <div class="ticket-card">
                        <h3 class="fw-bold mb-3">Premium Pass</h3>
                        <h2 class="display-4 fw-bold mb-3">₦75,000</h2>
                        <p class="mb-4">Full access to all sessions, workshops, networking events, and exclusive after-party</p>
                        <a href="#" class="btn btn-light btn-lg w-100">Buy Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Event Video -->
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Event Highlights</h3>
                    <div class="video-container">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <p class="mt-3 text-muted">Watch the highlights from last year's summit to get a taste of what to expect.</p>
                </div>
                
                <!-- Event Details -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">About the Summit</h3>
                    <p class="mb-3">The Nigeria Tech Summit is the premier gathering for technology professionals, entrepreneurs, investors, and policymakers across Africa. Now in its 8th year, the summit brings together the brightest minds to shape the future of technology in Nigeria and beyond.</p>
                    <p class="mb-3">This year's theme is "Building the Digital Future: Innovation, Inclusion, and Impact." We'll explore how technology can drive economic growth, create opportunities, and solve pressing challenges across the continent.</p>
                    <p>With over 200 speakers, 100+ exhibitors, and 5,000+ attendees, the Nigeria Tech Summit is the place to connect, learn, and discover the next big thing in African tech.</p>
                </div>

                <!-- Event Schedule -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Event Schedule</h3>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <h5 class="fw-bold text-primary-accent">Day 1</h5>
                            <p class="fw-bold mb-1">Opening Keynote</p>
                            <p class="text-muted small mb-2">9:00 AM - 10:30 AM</p>
                            <p class="fw-bold mb-1">Startup Pitch Competition</p>
                            <p class="text-muted small mb-2">11:00 AM - 1:00 PM</p>
                            <p class="fw-bold mb-1">AI & Machine Learning Track</p>
                            <p class="text-muted small mb-0">2:00 PM - 5:00 PM</p>
                        </div>
                        <div class="col-md-4 mb-4">
                            <h5 class="fw-bold text-primary-accent">Day 2</h5>
                            <p class="fw-bold mb-1">Fintech Innovation Panel</p>
                            <p class="text-muted small mb-2">9:30 AM - 11:00 AM</p>
                            <p class="fw-bold mb-1">Women in Tech Leadership</p>
                            <p class="text-muted small mb-2">11:30 AM - 1:00 PM</p>
                            <p class="fw-bold mb-1">Blockchain & Web3 Workshop</p>
                            <p class="text-muted small mb-0">2:30 PM - 5:00 PM</p>
                        </div>
                        <div class="col-md-4 mb-4">
                            <h5 class="fw-bold text-primary-accent">Day 3</h5>
                            <p class="fw-bold mb-1">Investor Connect</p>
                            <p class="text-muted small mb-2">10:00 AM - 12:00 PM</p>
                            <p class="fw-bold mb-1">Closing Ceremony</p>
                            <p class="text-muted small mb-2">2:00 PM - 4:00 PM</p>
                            <p class="fw-bold mb-1">Networking After-Party</p>
                            <p class="text-muted small mb-0">6:00 PM - 10:00 PM</p>
                        </div>
                    </div>
                </div>


                <!-- Speakers -->
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

                <!-- Gallery -->
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
                </div>

                <!-- Articles -->
                <div class="info-card" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="fw-bold mb-4">Related Articles</h3>
                    <div class="border-start border-primary border-3 ps-3 mb-4">
                        <h5 class="fw-bold">How AI is Transforming Healthcare in Nigeria</h5>
                        <p class="text-muted small mb-2">Exploring the impact of machine learning on medical diagnosis and treatment in Nigerian hospitals.</p>
                        <span class="text-muted small">Published: March 10, 2025</span>
                    </div>
                    <div class="border-start border-primary border-3 ps-3">
                        <h5 class="fw-bold">The Rise of AI Communities in Lagos</h5>
                        <p class="text-muted small mb-2">How meetups and communities are driving AI innovation in Nigeria's tech capital.</p>
                        <span class="text-muted small">Published: February 28, 2025</span>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event Info -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Event Information</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Date & Time</h6>
                        <p class="mb-0">November 15-17, 2025<br>9:00 AM - 6:00 PM Daily</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Location</h6>
                        <p class="mb-0">Landmark Centre<br>3-5 Water Corporation Drive<br>Victoria Island, Lagos</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Expected Participants</h6>
                        <p class="mb-0">5,000+</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Contact Information</h6>
                        <p class="mb-0">info@nigeriatechsummit.ng<br>+234 801 234 5678</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Event Categories</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge bg-primary">Conference</span>
                            <span class="badge bg-primary">Networking</span>
                            <span class="badge bg-primary">Technology</span>
                            <span class="badge bg-primary">Innovation</span>
                        </div>
                    </div>
                </div>
                
                <!-- Organizers -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Event Organizers</h4>
                    <div class="organizer-card">
                        <div class="organizer-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Tech Events NG</h5>
                        <p class="text-muted small">Premier technology event organizers in Nigeria</p>
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
                
                <!-- Social Media -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Follow the Event</h4>
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
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Location Map -->
                <div class="info-card" data-aos="fade-left" data-aos-delay="300">
                    <h4 class="fw-bold mb-4">Location Map</h4>
                    <div class="bg-light rounded-3 p-3 mb-3 text-center">
                        <i class="fas fa-map-marked-alt text-primary-accent" style="font-size: 3rem;"></i>
                        <p class="mt-2 mb-0">Landmark Centre, Victoria Island, Lagos</p>
                    </div>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View on Map
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once('footer.php'); ?>

</body>
</html>