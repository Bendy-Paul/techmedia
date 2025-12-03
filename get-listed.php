<!-- get-listed.html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Listed | Tech Media Directory</title>

    <?php include_once('links.php') ?>

    <style>
        .pricing-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .pricing-card.featured {
            border: 2px solid var(--primary-accent);
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-5px);
        }

        .pricing-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .pricing-price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-accent);
            margin: 1rem 0;
        }

        .pricing-period {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .pricing-features {
            margin: 2rem 0;
        }

        .pricing-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .pricing-feature i {
            color: var(--primary-accent);
            margin-right: 0.75rem;
            font-size: 0.8rem;
        }

        .feature-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--primary-accent);
            color: white;
            padding: 0.5rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 0 1rem 0 1rem;
        }

        .form-section {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 3rem;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }

        .step-indicator:before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: var(--primary-accent);
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }
    </style>
</head>

<body>

    <?php include_once('event-ticker.php')?>
    <?php include_once('header.php')?>

    <!-- Hero Section -->
    <section class="hero-section" style="min-height: 40vh;">
        <div class="hero-bg"></div>
        <div class="grid-overlay"></div>

        <div class="hero-content" data-aos="zoom-out" data-aos-duration="1200">
            <h1 class="hero-title">Get Your Business <br><span class="text-primary-accent">Listed Today</span></h1>
            <p class="hero-subtitle">Join Nigeria's premier tech directory and connect with clients, partners, and investors.</p>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h5 class="text-primary-accent text-uppercase">Pricing Plans</h5>
                <h2 class="fw-bold">Choose Your Listing Tier</h2>
                <p class="text-muted">Select the plan that works best for your business needs</p>
            </div>

            <div class="row justify-content-center">
                <!-- Free Tier -->
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h4 class="fw-bold">Basic</h4>
                            <div class="pricing-price">₦0</div>
                            <div class="pricing-period">Free forever</div>
                        </div>
                        <div class="pricing-features">
                            <div class="pricing-feature"><i class="fas fa-check"></i> Basic company profile</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Category listing</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Contact information</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Appear in search results</div>
                            <div class="pricing-feature text-muted"><i class="fas fa-times"></i> <del>Featured placement</del></div>
                            <div class="pricing-feature text-muted"><i class="fas fa-times"></i> <del>Premium analytics</del></div>
                        </div>
                        <button class="btn btn-outline-primary w-100">Get Started</button>
                    </div>
                </div>

                <!-- Silver Tier -->
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-card featured">
                        <div class="feature-badge">Most Popular</div>
                        <div class="pricing-header">
                            <h4 class="fw-bold">Silver</h4>
                            <div class="pricing-price">₦15,000</div>
                            <div class="pricing-period">per month</div>
                        </div>
                        <div class="pricing-features">
                            <div class="pricing-feature"><i class="fas fa-check"></i> Everything in Basic</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Enhanced profile with logo</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Up to 5 service categories</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Basic analytics dashboard</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Featured in category listings</div>
                            <div class="pricing-feature text-muted"><i class="fas fa-times"></i> <del>Premium support</del></div>
                        </div>
                        <button class="btn btn-primary w-100">Get Started</button>
                    </div>
                </div>

                <!-- Gold Tier -->
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h4 class="fw-bold">Gold</h4>
                            <div class="pricing-price">₦35,000</div>
                            <div class="pricing-period">per month</div>
                        </div>
                        <div class="pricing-features">
                            <div class="pricing-feature"><i class="fas fa-check"></i> Everything in Silver</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Premium placement on homepage</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Advanced analytics & insights</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Dedicated account manager</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Featured in newsletter</div>
                            <div class="pricing-feature"><i class="fas fa-check"></i> Priority customer support</div>
                        </div>
                        <button class="btn btn-outline-primary w-100">Get Started</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="form-section" data-aos="fade-up">
                        <h3 class="fw-bold mb-4 text-center">Apply for Listing</h3>

                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step active">
                                <div class="step-number">1</div>
                                <div class="step-label">Company Info</div>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-label">Services</div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-label">Review</div>
                            </div>
                        </div>

                        <!-- Step 1: Company Information -->
                        <div class="form-step active" id="step1">
                            <h5 class="mb-4">Company Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="companyName" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="companyName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Company Description *</label>
                                    <textarea class="form-control" id="description" rows="4" required></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="logo" class="form-label">Company Logo</label>
                                    <input type="file" class="form-control" id="logo" accept="image/*">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-primary" onclick="nextStep(2)">Next <i class="fas fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 2: Services -->
                        <div class="form-step" id="step2">
                            <h5 class="mb-4">Services & Categories</h5>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Primary Category *</label>
                                    <select class="form-select" required>
                                        <option value="">Select a category</option>
                                        <option value="software">Software & Development</option>
                                        <option value="hardware">Hardware & Infrastructure</option>
                                        <option value="data">Data Science & AI</option>
                                        <option value="marketing">Digital Marketing</option>
                                        <option value="fintech">Fintech</option>
                                        <option value="healthtech">HealthTech</option>
                                        <option value="edutech">EduTech</option>
                                        <option value="agritech">AgriTech</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Additional Categories</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="category1">
                                        <label class="form-check-label" for="category1">Web Development</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="category2">
                                        <label class="form-check-label" for="category2">Mobile Applications</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="category3">
                                        <label class="form-check-label" for="category3">Cloud Services</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="category4">
                                        <label class="form-check-label" for="category4">Data Analytics</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="services" class="form-label">Services Offered *</label>
                                    <textarea class="form-control" id="services" rows="4" required placeholder="Describe the services your company offers"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-outline-secondary" onclick="prevStep(1)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                                <button class="btn btn-primary" onclick="nextStep(3)">Next <i class="fas fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 3: Review & Submit -->
                        <div class="form-step" id="step3">
                            <h5 class="mb-4">Review & Submit</h5>
                            <div class="card bg-light border-0 p-4 mb-4">
                                <h6 class="fw-bold">Company Information</h6>
                                <p id="review-company" class="mb-2">-</p>
                                <p id="review-email" class="mb-2">-</p>
                                <p id="review-description" class="mb-0">-</p>
                            </div>
                            <div class="card bg-light border-0 p-4">
                                <h6 class="fw-bold">Selected Categories</h6>
                                <p id="review-categories" class="mb-0">-</p>
                            </div>
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="terms-of-service.html" class="text-primary-accent">Terms of Service</a> and <a href="privacy-policy.html" class="text-primary-accent">Privacy Policy</a>
                                </label>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-outline-secondary" onclick="prevStep(2)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                                <button class="btn btn-primary" id="submit-btn">Submit Application</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center mb-5 fw-bold">Frequently Asked Questions</h2>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    How long does approval take?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted ps-4">
                                    Free listings are typically approved within 24-48 hours. Paid listings are prioritized and usually approved within 4-6 hours during business days.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Can I upgrade my plan later?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted ps-4">
                                    Yes, you can upgrade your plan at any time. When you upgrade, you'll be charged the prorated difference for the remainder of your billing cycle.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted ps-4">
                                    We accept bank transfers, credit/debit cards, and popular payment platforms like Paystack and Flutterwave.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php') ?>

    <script>
        $(document).ready(function() {
            // Form submission
            $('#submit-btn').click(function() {
                // Update review section
                $('#review-company').text($('#companyName').val());
                $('#review-email').text($('#email').val());
                $('#review-description').text($('#description').val());

                // Get selected categories
                let categories = [];
                $('input[type="checkbox"]:checked').each(function() {
                    categories.push($(this).next('label').text());
                });
                $('#review-categories').text(categories.join(', '));

                // Validate form
                if ($('#terms').is(':checked')) {
                    alert('Application submitted successfully! We will review your application and get back to you soon.');
                    // In a real application, you would submit the form data to a server here
                } else {
                    alert('Please agree to the Terms of Service and Privacy Policy.');
                }
            });
        });

        function nextStep(step) {
            // Hide all steps
            $('.form-step').removeClass('active');
            // Show current step
            $('#step' + step).addClass('active');

            // Update step indicator
            $('.step').removeClass('active');
            $('.step:nth-child(' + step + ')').addClass('active');
        }

        function prevStep(step) {
            nextStep(step);
        }
    </script>
</body>

</html>