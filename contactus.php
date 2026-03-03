<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | RedTrack</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        /* HEADER */
        .page-header {
            background: linear-gradient(135deg, #c70039, #ff4d6d);
            color: white;
            padding: 70px 0;
            text-align: center;
        }

        /* CARDS */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        /* BUTTON */
        .btn-main {
            background: #c70039;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-main:hover {
            background: #a0002e;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(199,0,57,0.4);
        }

        /* ICONS */
        .contact-icon {
            font-size: 20px;
            color: #c70039;
            margin-right: 10px;
        }

        .social-icons a {
            color: #c70039;
            font-size: 22px;
            margin-right: 15px;
            transition: transform 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
        }

        footer {
            background: #212529;
            color: #bbb;
        }

        /* ANIMATIONS */
        .fade-down {
            animation: fadeDown 1s ease forwards;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(40px);
            animation: fadeUp 1s ease forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark fade-down">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">RedTrack</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                <a class="nav-link mx-3" href="homepage.php">Home</a>
                <a class="nav-link mx-3" href="aboutus.php">About Us</a>
                <a class="nav-link mx-3 active text-danger fw-bold" href="#">Contact Us</a>
                <!-- <a class="nav-link mx-3" href="#">Donor List</a> -->
                <a class="nav-link mx-3" href="searchdonor.php">Search Donor</a>
                <a class="nav-link mx-3" href="myaccount.php">My Account</a>
            </div>
        </div>
    </div>
</nav>

<!-- HEADER -->
<section class="page-header fade-up">
    <div class="container">
        <h1>Contact RedTrack</h1>
        <p class="mt-2">Connecting donors, recipients, and blood banks to save lives</p>
    </div>
</section>

<!-- CONTACT SECTION -->
<div class="container my-5">
    <div class="row g-4">

        <!-- CONTACT INFO -->
        <div class="col-md-5">
            <div class="card p-4 h-100 fade-up delay-1">
                <h4>Contact Information</h4>

                <p class="mt-3"><i class="fas fa-location-dot contact-icon"></i>Kathmandu, Nepal</p>
                <p><i class="fas fa-phone contact-icon"></i>+977 98XXXXXXXX</p>
                <p><i class="fas fa-envelope contact-icon"></i>redtrack@gmail.com</p>

                <hr>

                <h6>Office Hours</h6>
                <p>Sunday – Friday: 9:00 AM – 6:00 PM</p>
                <p>Saturday: Closed</p>

                <hr>

                <h6>Emergency Blood Request</h6>
                <p>Contact us immediately for urgent blood requirements.</p>

                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>

        <!-- CONTACT FORM -->
        <div class="col-md-7">
            <div class="card p-4 fade-up delay-2">
                <h4>Send Us a Message</h4>
                <p class="text-muted">We will respond as soon as possible</p>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" placeholder="Enter your name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="5" placeholder="Write your message..." required></textarea>
                    </div>

                    <button class="btn btn-main px-4">Send Message</button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- FAQ -->
<div class="container my-5">
    <div class="card p-4 fade-up delay-1">
        <h4 class="mb-3">Frequently Asked Questions</h4>

        <div class="accordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button">Who can donate blood?</button>
                </h2>
                <div class="accordion-body">
                    Healthy individuals aged 18–60 and weighing over 50kg can donate blood.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAP -->
<div class="container my-5">
    <div class="card p-4 fade-up delay-2">
        <h4 class="mb-3">Find Us</h4>
        <iframe src="https://www.google.com/maps?q=Kathmandu,Nepal&output=embed"
            width="100%" height="300" style="border:0;" loading="lazy"></iframe>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-4 fade-up delay-3">
    <p class="mb-1">© 2025 RedTrack – Blood Donation Management System</p>
    <small>Designed for academic and social impact purposes</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
