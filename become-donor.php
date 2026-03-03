<?php
session_start();

// Prevent minor warnings
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "*rajan12345#";
$dbname = "list_donor";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$isAlreadyDonor = false;
$donorInfo = null;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $connUser = new mysqli($servername, $username, $password, "store_data_login_signup");
    if ($connUser->connect_error) {
        die("Connection failed: " . $connUser->connect_error);
    }

    $stmt = $connUser->prepare("SELECT email, username FROM sign_up WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();
    $connUser->close();

    if ($userData) {
        $userEmail = $userData['email'];

        $checkDonor = $conn->prepare("SELECT * FROM donors WHERE email = ?");
        $checkDonor->bind_param("s", $userEmail);

        $checkDonor->execute();
        $donorResult = $checkDonor->get_result();

        if ($donorResult->num_rows > 0) {
            $isAlreadyDonor = true;
            $donorInfo = $donorResult->fetch_assoc();
        }
        $checkDonor->close();
    }
}

// ===============================
// Handle Form Submission
// ===============================
if (isset($_POST['register_donor']) && !$isAlreadyDonor) {

    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $blood_group = $_POST['blood_group'];
    $phone = trim($_POST['phone']); // sanitize phone
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $address = $_POST['address'];
    $district = $_POST['district'];

    /*
    ====================================
    PHONE VALIDATION (Nepal Numbers)
    Must start with 98 or 97
    Must be exactly 10 digits
    ====================================
    */
    if (!preg_match('/^(98|97)\d{8}$/', $phone)) {

        $message = "<div class='alert alert-danger'>
        <i class='fa-solid fa-times-circle me-2'></i>
        Invalid phone number! Enter valid Nepali number (98XXXXXXXX or 97XXXXXXXX).
        </div>";
    } else {

        // Check again if email or phone already exists
        $checkExisting = $conn->prepare("SELECT * FROM donors WHERE email = ? OR phone = ?");
        $checkExisting->bind_param("ss", $email, $phone);
        $checkExisting->execute();
        $existingResult = $checkExisting->get_result();

        if ($existingResult->num_rows > 0) {

            $message = "<div class='alert alert-warning'>
            <i class='fa-solid fa-exclamation-triangle me-2'></i>
            This email or phone number is already registered as a donor!
            </div>";
        } else {

            $stmt = $conn->prepare("INSERT INTO donors 
                (fullname, gender, age, blood_group, phone, email, address, district, status, availability) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Available')");

            $stmt->bind_param(
                "ssisssss",
                $fullname,
                $gender,
                $age,
                $blood_group,
                $phone,
                $email,
                $address,
                $district
            );

            if ($stmt->execute()) {

                $message = "<div class='alert alert-success'>
                <i class='fa-solid fa-check-circle me-2'></i>
                Registration successful! Please wait for admin approval.
                </div>";

                $isAlreadyDonor = true;
                $donorInfo = [
                    'fullname' => $fullname,
                    'blood_group' => $blood_group,
                    'status' => 'Pending'
                ];
            } else {
                $message = "<div class='alert alert-danger'>
                <i class='fa-solid fa-times-circle me-2'></i>
                Error: " . $conn->error . "
                </div>";
            }

            $stmt->close();
        }

        $checkExisting->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Donor | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .reg-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
        }

        .header-gradient {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .donor-status-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 20px 0;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-approved {
            background: #28a745;
            color: #fff;
        }

        .status-rejected {
            background: #dc3545;
            color: #fff;
        }

        .donor-info-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">RedTrack</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link mx-3" href="index.php">Home</a>
                    <a class="nav-link mx-3" href="aboutus.php">About Us</a>
                    <a class="nav-link mx-3" href="contactus.php">Contact Us</a>
                    <!-- <a class="nav-link mx-3" href="#">Donar List</a> -->
                    <a class="nav-link mx-3" href="searchdonor.php">Search Donar</a>
                    <a class="nav-link mx-3" href="myaccount.php">My Account</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($isAlreadyDonor && $donorInfo): ?>
                    <!-- Already a Donor Card -->
                    <div class="donor-status-card">
                        <i class="fa-solid fa-heart-circle-check fa-5x mb-4" style="opacity: 0.9;"></i>
                        <h2 class="fw-bold mb-3">You're Already a Registered Donor!</h2>
                        <p class="fs-5 mb-4">Thank you for being a life saver, <?= htmlspecialchars($donorInfo['fullname']) ?>!</p>

                        <div class="donor-info-box">
                            <div class="row text-center">
                                <div class="col-md-6 mb-3">
                                    <h6 class="mb-2">Blood Group</h6>
                                    <h3 class="fw-bold"><i class="fa-solid fa-droplet me-2"></i><?= htmlspecialchars($donorInfo['blood_group']) ?></h3>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="mb-2">Registration Status</h6>
                                    <span class="status-badge status-<?= strtolower($donorInfo['status']) ?>">
                                        <?php if ($donorInfo['status'] == 'Pending'): ?>
                                            <i class="fa-solid fa-clock me-2"></i>Pending Approval
                                        <?php elseif ($donorInfo['status'] == 'Approved'): ?>
                                            <i class="fa-solid fa-check-circle me-2"></i>Approved
                                        <?php else: ?>
                                            <i class="fa-solid fa-times-circle me-2"></i><?= $donorInfo['status'] ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if ($donorInfo['status'] == 'Pending'): ?>
                            <p class="mt-4 mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Your registration is under review. You'll be notified once approved by our admin team.
                            </p>
                        <?php elseif ($donorInfo['status'] == 'Approved'): ?>
                            <p class="mt-4 mb-0">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                You're now an active blood donor! People in need can contact you.
                            </p>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="myaccount.php" class="btn btn-light btn-lg px-5 me-2" style="border-radius: 50px;">
                                <i class="fa-solid fa-user me-2"></i>My Account
                            </a>
                            <a href="index.php" class="btn btn-outline-light btn-lg px-5" style="border-radius: 50px;">
                                <i class="fa-solid fa-home me-2"></i>Go Home
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Registration Form -->
                    <div class="reg-card">
                        <div class="header-gradient">
                            <i class="fa-solid fa-heart-pulse fa-3x mb-3"></i>
                            <h2 class="fw-bold">Become a Life Saver</h2>
                            <p class="mb-0">Fill in your details to register as a blood donor</p>
                        </div>

                        <div class="p-4 p-md-5">
                            <?php echo $message; ?>

                            <form action="" method="POST" class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label"><i class="fa-solid fa-user me-2"></i>Full Name</label>
                                    <input type="text" name="fullname" class="form-control" placeholder="John Doe" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-venus-mars me-2"></i>Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-calendar-days me-2"></i>Age</label>
                                    <input type="number" name="age" class="form-control" min="18" max="65" placeholder="Min 18" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-droplet me-2 text-danger"></i>Blood Group</label>
                                    <select name="blood_group" class="form-select" required>
                                        <option value="">Select Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-phone me-2"></i>Phone Number</label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        class="form-control"
                                        placeholder="98XXXXXXXX"
                                        pattern="^(98|97)\d{8}$"
                                        title="Enter valid Nepali number (98XXXXXXXX or 97XXXXXXXX)"
                                        required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label"><i class="fa-solid fa-envelope me-2"></i>Email Address (Optional)</label>
                                    <input type="email" name="email" class="form-control" placeholder="example@mail.com">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-map-location-dot me-2"></i>District</label>
                                    <input type="text" name="district" class="form-control" placeholder="e.g. Kathmandu" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-house me-2"></i>Full Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="Street, Ward No." required>
                                </div>

                                <div class="col-md-12 mt-5">
                                    <button type="submit" name="register_donor" class="btn btn-danger btn-lg w-100 shadow-sm" style="border-radius: 10px; padding: 15px;">
                                        <i class="fa-solid fa-heart-circle-plus me-2"></i>Register as Donor
                                    </button>
                                    <p class="text-center text-muted mt-3 small">
                                        By registering, you agree to be contacted by people in need of blood.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>