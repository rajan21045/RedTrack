<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us – RedTrack</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Roboto:wght@300;400;500;700&display=swap');

        :root {
            --primary-red: #E51423;
            --secondary-dark: #1E2A3B;
            --background-light: #f7f9fc;
            --text-muted: #7f8c8d;
            --card-dark-bg: #2d3e50;
            --card-button-gray: #55606e;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background-light);
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--secondary-dark);
        }

        .section-header-custom {
            position: relative;
            display: inline-block;
        }

        .section-header-custom::after {
            content: '';
            width: 80px;
            height: 5px;
            background: var(--primary-red);
            position: absolute;
            left: 50%;
            bottom: -15px;
            transform: translateX(-50%);
        }

        /* HERO */
        .hero {
            background: #fff;
            padding: 6rem 0;
        }

        .hero h1 {
            font-size: 3.8rem;
            font-weight: 900;
        }

        .hero-img {
            height: 380px;
            background: url('img/redtrack.png') center/cover no-repeat;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .15);
        }

        /* STATS */
        .stats-box {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .stats-box h2 {
            color: var(--primary-red);
            font-weight: 900;
        }

        /* COMMITMENTS */
        .commitment-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            transition: .3s;
        }

        .commitment-card:hover {
            transform: translateY(-8px);
        }

        .commitment-card i {
            font-size: 2.5rem;
            color: var(--primary-red);
        }

        /* PROCESS */
        .steps-card {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            display: flex;
            gap: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        .step-number {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary-red);
            background: rgba(229, 20, 35, .1);
            padding: 10px 18px;
            border-radius: 8px;
        }

        /* INFO SECTIONS */
        .info-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .08);
        }

        /* BEAUTIFUL FEATURE SECTIONS */
        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
            transition: transform .3s ease, box-shadow .3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: rgba(229, 20, 35, 0.1);
            color: var(--primary-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 1.2rem;
        }

        .feature-card h5 {
            font-weight: 800;
            margin-bottom: 0.7rem;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Soft section background */
        .gradient-soft {
            background: linear-gradient(135deg,
                    rgba(229, 20, 35, 0.04),
                    rgba(30, 42, 59, 0.03));
        }

        /* FAQ */
        .accordion-button:not(.collapsed) {
            background: rgba(229, 20, 35, .1);
            color: #000;
        }

        /* TEAM */
        .team-section {
            background-color: var(--background-light);
            padding-top: 5rem;
            padding-bottom: 5rem;
        }

        .team-card-v2 {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            margin-bottom: 16px;
            background-color: var(--card-dark-bg);
            border-radius: 0;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .team-card-v2:hover {
            transform: none;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.3);
        }

        .team-card-v2 img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 0;
        }

        .team-card-v2 .card-content {
            padding: 20px 20px;
            text-align: left;
            color: #ffffff;
        }

        .team-card-v2 h6 {
            font-weight: 700;
            margin-bottom: 0.1rem;
            color: #ffffff;
            font-size: 1.5rem;
            margin-top: 10px;
        }

        .team-card-v2 .title {
            color: #bdc3c7;
            font-weight: 400;
            font-size: 1rem;
            margin-bottom: 15px;
            display: block;
        }

        .team-card-v2 p {
            font-size: 0.95rem;
            color: #ecf0f1;
            margin-bottom: 10px;
        }

        .team-card-v2 .contact-button {
            border: none;
            outline: 0;
            display: block;
            padding: 10px;
            color: white;
            background-color: var(--card-button-gray);
            text-align: center;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            border-radius: 0;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .team-card-v2 .contact-button:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>

<body>
     <!-- NavBar Starts From Here -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">

            <!-- Brand / Logo (Left Side) -->
            <a class="navbar-brand" href="#">RedTrack</a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Items -->
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <!-- ms-auto pushes items to the RIGHT -->
                    <a class="nav-link mx-3" aria-current="page" href="homepage.php" >Home</a>
                    <a class="nav-link mx-3 active text-danger fw-bold" aria-current="page" href="#">About Us</a>
                    <a class="nav-link mx-3" href="contactus.php">Contact Us</a>
                    <!-- <a class="nav-link mx-3" href="#">Donar List</a> -->
                    <a class="nav-link mx-3" href="searchdonor.php">Search Donar</a>
                    <a class="nav-link mx-3" href="myaccount.php">My Account</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7" data-aos="fade-right">
                    <p class="text-danger fw-bold">DIGITAL BLOOD DONATION PLATFORM</p>
                    <h1>Saving Lives Through <span class="text-danger">Smart Blood Management</span></h1>
                    <p class="lead text-muted">
                        RedTrack is a centralized blood donation management system that connects donors,
                        hospitals, and recipients efficiently during critical moments.
                    </p>
                </div>
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="hero-img"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="0">
                    <div class="stats-box">
                        <h2>5,000+</h2>
                        <p class="fw-bold mb-0">Lives Positively Impacted Annually</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="150">
                    <div class="stats-box">
                        <h2>15</h2>
                        <p class="fw-bold mb-0">Regions Covered by Our Network</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stats-box">
                        <h2>95%</h2>
                        <p class="fw-bold mb-0">Average Donor Response Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE COMMITMENTS -->
    <section class="py-5" data-aos="fade-up">
        <div class="container text-center">
            <div class="section-header-custom mb-5">
                <h2>Our Core Commitments</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="commitment-card"><i class="fas fa-heartbeat"></i>
                        <h5>Life First</h5>
                        <p>Every feature prioritizes saving lives.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="commitment-card"><i class="fas fa-bolt"></i>
                        <h5>Rapid Response</h5>
                        <p>Instant donor matching during emergencies.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="commitment-card"><i class="fas fa-shield-alt"></i>
                        <h5>Secure Data</h5>
                        <p>Protected donor and patient information.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="commitment-card"><i class="fas fa-users"></i>
                        <h5>Community</h5>
                        <p>Empowering voluntary blood donors.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROCESS -->
    <section class="py-5 bg-white" data-aos="fade-up">
        <div class="container">
            <div class="section-header-custom text-center mb-5">
                <h2>Our Impactful Process</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="steps-card"><span class="step-number">01</span>
                        <div>
                            <h5>Register</h5>
                            <p>Donors securely register and update availability.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="steps-card"><span class="step-number">02</span>
                        <div>
                            <h5>Request</h5>
                            <p>Hospitals raise urgent blood requests.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-right">
                    <div class="steps-card"><span class="step-number">03</span>
                        <div>
                            <h5>Match</h5>
                            <p>System finds nearest eligible donors.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="steps-card"><span class="step-number">04</span>
                        <div>
                            <h5>Donate</h5>
                            <p>Fast coordination saves precious time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TEAM (UNCHANGED) -->
    <section class="team-section" data-aos="fade-up">
        <div class="container">

            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-header-custom">
                    <h2>Meet the Dedicated Team</h2>
                </div>
                <p class="lead text-muted mt-4">The professionals and technical experts powering RedTrack.</p>
            </div>

            <div class="row g-4 justify-content-center">

                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <div class="team-card-v2 h-100">
                        <img src="img/rajan.JPG" alt="Rajan Poudel" class="img-fluid">
                        <div class="card-content">
                            <h6>Rajan Poudel</h6>
                            <span class="title">CEO &amp; Founder</span>
                            <p>Passionate leader focused on innovation and impactful social services.</p>
                            <p class="text-light">raajan.works@gmail.com</p>
                            <button class="contact-button">Contact</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="team-card-v2 h-100">
                        <img src="img/sasinn.jpg" alt="Sasin Godar" class="img-fluid">
                        <div class="card-content">
                            <h6>Sasin Godar</h6>
                            <span class="title">CTO</span>
                            <p>Creative mind behind the visual and user experience of our platform.</p>
                            <p class="text-light">saasin.works@gmail.com</p>
                            <button class="contact-button">Contact</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-card-v2 h-100">
                        <img src="img/prabin.jpg" alt="Prabin Thapa" class="img-fluid">
                        <div class="card-content">
                            <h6>Prabin Thapa</h6>
                            <span class="title">Designer</span>
                            <p>Designs interfaces that are simple, beautiful, and user-friendly.</p>
                            <p class="text-light">prabin.works@gmail.com</p>
                            <button class="contact-button">Contact</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TECHNOLOGY -->
    <section class="py-5 gradient-soft" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-header-custom">
                    <h2>Technology Driving Reliability</h2>
                </div>
                <p class="lead text-muted mt-4">
                    Built on modern, secure, and scalable technologies to perform flawlessly under pressure.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-database"></i></div>
                        <h5>Secure Data Architecture</h5>
                        <p>
                            Encrypted databases ensure donor and hospital information
                            remains protected at all times.
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="150">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <h5>Real-Time Matching</h5>
                        <p>
                            Smart algorithms instantly connect urgent requests
                            with nearby eligible donors.
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-cloud"></i></div>
                        <h5>High Availability System</h5>
                        <p>
                            Cloud-ready infrastructure ensures uninterrupted service
                            during emergencies.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUSTAINABILITY -->
    <section class="py-5 bg-white" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-header-custom">
                    <h2>Sustainability & Ethical Practice</h2>
                </div>
                <p class="lead text-muted mt-4">
                    Responsible technology designed to protect donors, respect privacy,
                    and sustain long-term impact.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-hand-holding-heart"></i></div>
                        <h5>Ethical Donation Model</h5>
                        <p>
                            100% voluntary blood donation with transparent
                            usage and no commercial exploitation.
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="150">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
                        <h5>Privacy First</h5>
                        <p>
                            Strict access control and data minimization
                            protect donor identity and medical details.
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-leaf"></i></div>
                        <h5>Long-Term Sustainability</h5>
                        <p>
                            Promoting awareness, responsible donation frequency,
                            and community trust for the future.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="section-header-custom text-center mb-5">
                <h2>Frequently Asked Questions</h2>
            </div>

            <div class="accordion" id="faq">
                <div class="accordion-item" data-aos="fade-up" data-aos-delay="0">
                    <h2 class="accordion-header">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#q1">
                            Is blood donation safe?
                        </button>
                    </h2>
                    <div id="q1" class="accordion-collapse collapse show">
                        <div class="accordion-body">Yes, blood donation is safe and medically supervised.</div>
                    </div>
                </div>

                <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#q2">
                            Who can donate blood?
                        </button>
                    </h2>
                    <div id="q2" class="accordion-collapse collapse">
                        <div class="accordion-body">Healthy individuals aged 18–65 can donate.</div>
                    </div>
                </div>

                <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#q3">
                            How often can I donate?
                        </button>
                    </h2>
                    <div id="q3" class="accordion-collapse collapse">
                        <div class="accordion-body">Typically every 3 months.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="py-5 bg-white" data-aos="fade-up">
        <div class="container text-center">
            <div class="section-header-custom mb-4">
                <h2>Get In Touch</h2>
            </div>
            <p class="lead text-muted">
                Have questions or want to collaborate? Reach out to us anytime.
            </p>
            <p class="fw-bold text-danger">support@redtrack.com</p>
        </div>
    </section>

    <footer class="bg-dark text-secondary text-center py-3">
        &copy; 2025 RedTrack | Blood Donation Management System | Academic Project
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 900,
            once: true
        });
    </script>
</body>

</html>
