<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CDN Link Starts Here -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Bootstrap CDN Link Ends Here -->

    <!--Google Fonts Link Starts Here -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Manrope:wght@400;700&display=swap"
        rel="stylesheet">
    <!-- Google Fonts Link Ends Here -->

    <!-- Font Awesome CDN Link Start Here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI6QpRohGBreCFkKx36l9/WSAWd/i90a29YcM6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- font awesone cdn ends here -->

    <!-- CSS Link Starts Here -->
    <link rel="stylesheet" href="style.css">
    <!-- CSS Link Ends Here -->

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">


    <title>RedTrack - HomePage</title>
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
                    <a class="nav-link mx-3 active text-danger fw-bold" aria-current="page" href="#">Home</a>
                    <a class="nav-link mx-3" href="aboutus.php">About Us</a>
                    <a class="nav-link mx-3" href="contactus.php">Contact Us</a>
                    <!-- <a class="nav-link mx-3" href="#">Donar List</a> -->
                    <a class="nav-link mx-3" href="searchdonor.php">Search Donar</a>
                    <a class="nav-link mx-3" href="myaccount.php">My Account</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Hero Section Starts From Here -->
    <section class="hero">
        <div class="hero-content" data-aos="fade-right">
            <p>Blood is meant to circulate.</p>
            <p>Pass it around.</p>
            <p id="pass">There are many countries that do not have an adequate number of blood donors and continue to
                struggle <br>with shortages in blood supply. As a result, countless patients face delays in receiving
                life-saving treatment. <br> Our mission is to bridge this gap by ensuring timely availability of safe
                and
                sufficient blood for those in need. <br> Together, through awareness, donation, and community support,
                we can
                help save lives and strengthen <br> healthcare systems around the world.</p>
            <button type="button" class="btn btn-dark" onclick="window.location.href='searchdonor.php'">Search Donor</button>
            <button type="button" class="btn btn-dark" onclick="window.location.href='become-donor.php'">Become A Donor</button>
        </div>
    </section>
    <!-- Hero Section Ends Here -->

    <!-- About Our Section Starts From Here -->
    <section class="about-system" data-aos="fade-up">
        <h2><b>About Our System </b></h2>
        <p class="about-description">
            The Blood Management System helps connect blood donors with recipients quickly and safely.
            We ensure accurate information, verified donors, and an easy search system for emergencies.
        </p>

        <div class="about-features">

            <div class="feature-box" data-aos="zoom-in">
                <span class="check-icon">✔</span>
                <h3>Fast Search</h3>
                <p>Find compatible blood donors instantly using filters.</p>
            </div>

            <div class="feature-box" data-aos="zoom-in">
                <span class="check-icon">✔</span>
                <h3>Verified Donors</h3>
                <p>We maintain a secure and validated donor list.</p>
            </div>

            <div class="feature-box" data-aos="zoom-in">
                <span class="check-icon">✔</span>
                <h3>Easy to Use</h3>
                <p>Simple interface for both donors and recipients.</p>
            </div>

        </div>
    </section>
    <!-- About Our Section Ends Here -->
    <br><br>
    <!-- Why we use HLB -->

    <section class="why-section">
        <div class="why-container">

            <!-- Left Image -->
            <div class="why-image" data-aos="fade-right">
                <img src="img/whyhlb_image.png" alt="blood donation illustration">
            </div>

            <!-- Right Text -->
            <div class="why-text" data-aos="fade-left">
                <h2>Why RT?</h2>
                <p>Existing blood management system in Nepal is manual, cumbersome and inefficient.
                    Most blood banks record the information on blood collection/supply manually in registers.</p>

                <p>Maintaining blood stock inventory is tedious with laborious back-office paperwork and managing
                    information on availability and shortage of blood is a tall task.</p>

                <p>A social initiative for a smart, transparent and holistic blood management service from collection to
                    supply.</p>

                <p>When it comes to blood, right information at the right time can be the answer
                    to a life and death situation.</p>
            </div>

        </div>
    </section>



    <!-- Meet Our Team Section Starts Here -->
    <section class="team-section py-5">
        <div class="text-center mb-5">
            <h2 class="team-title">Meet Our Team</h2>
            <p class="team-subtitle">The people behind RedTrack</p>
        </div>

        <div class="ccontainer">
            <div class="row justify-content-center g-4">

                <!-- Team Member 1 -->
                <div class="col-md-3">
                    <div class="team-card" data-aos="flip-left">
                        <img src="img/rajan.JPG" alt="Rajan Poudel - CEO AND FOUNDER" class="team-img">
                        <h3 class="team-name">Rajan Poudel</h3>
                        <p class="team-role">CEO &amp; Founder</p>
                        <p class="team-desc">Passionate leader focused on innovation and impactful social services.</p>
                        <a href="mailto:example@example.com" class="team-btn">Contact</a>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="col-md-3">
                    <div class="team-card" data-aos="flip-left">
                        <img src="img/sasinn.jpg" alt="Sasin Godar - CTO" class="team-img">
                        <h3 class="team-name">Sasin Godar</h3>
                        <p class="team-role">CTO</p>
                        <p class="team-desc">Creative mind behind the visual and user experience of our platform.</p>
                        <a href="mailto:example@example.com" class="team-btn">Contact</a>
                    </div>
                </div>

                <!-- Team Member 3 -->

                <div class="col-md-3">
                    <div class="team-card" data-aos="flip-left">
                        <img src="img/prabin.jpg" alt="Prabin Thapa - Designer" class="team-img">
                        <h3 class="team-name">Prabin Thapa</h3>
                        <p class="team-role">Designer</p>
                        <p class="team-desc">Designs interfaces that are simple, beautiful, and user-friendly.</p>
                        <a href="mailto:example@example.com" class="team-btn">Contact</a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Meet Our Team Section Ends Here -->


    <!-- partnership section starts from here -->
    <section class="partners">
        <h2>Our Partners</h2>
        <p>We proudly collaborate with leading companies.</p>

        <div class="partner-logos">

            <div class="partner-item" data-aos="zoom-in-up">
                <div class="logo-circle">
                    <img src="img/pathao.png" alt="Pathao Logo">
                </div>
                <p class="partner-name">Pathao</p>
            </div>

            <div class="partner-item" data-aos="zoom-in-up">
                <div class="logo-circle">
                    <img src="img/plasma-connect-logo.png" alt="Plasma Logo">
                </div>
                <p class="partner-name">Plasma Connect</p>
            </div>

            <div class="partner-item" data-aos="zoom-in-up">
                <div class="logo-circle">
                    <img src="img/deerwalk.png" alt="Deerwalk Logo">
                </div>
                <p class="partner-name">Deerwalk</p>
            </div>

        </div>
    </section>
    <!-- partnership section ends here  -->

    <!-- event galllary section starts from here -->
    <div id="gal">
        <h2>Our Gallery</h2>
    </div>
    <div class="img-gal">
        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery1.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery2.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery3.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery4.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery5.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery6.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery7.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery8.jpg">
        </div>

        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery9.jpg">
        </div>
        <div class="img-box" data-aos="fade-up">
            <img src="img/gallery10.jpg">
        </div>

    </div>
    <!-- event gallery section ends here -->

    <!-- footer section starts from here -->
    <footer class="footer">
        <div class="con">
            <div class="row">

                <div class="footer-col">
                    <h4>About</h4>
                    <ul>
                        <li><a href="#">About the System</a></li>
                        <li><a href="#">How It Works</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="#">Request Blood</a></li>
                        <li><a href="#">Search Donors</a></li>
                        <li><a href="#">Nearest Blood Banks</a></li>
                        <li><a href="#">Donation Guidelines</a></li>
                        <li><a href="#">Emergency Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Become a Donor</a></li>
                        <li><a href="#">Blood Donation Camps</a></li>
                        <li><a href="#">Health & Tips</a></li>
                        <li><a href="#">Volunteer Program</a></li>
                    </ul>
                </div>

                <!-- <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i> </a>
                        <a href="#"><i class="fab fa-twitter"></i> </a>
                        <a href="#"><i class="fab fa-instagram"></i> </a>
                        <a href="#"><i class="fab fa-linkedin-in"></i> </a>
                    </div>
                </div> -->

            </div>
        </div>
    </footer>





    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: false
        });
    </script>

</body>

</html>