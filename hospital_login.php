<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['hospital_logged_in']) && $_SESSION['hospital_logged_in'] === true) {
    header("Location: hospital_dashboard.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create hospitals table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    registration_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTable);

$error = "";
$success = "";

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM hospitals WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $hospital = $result->fetch_assoc();
        if (password_verify($password, $hospital['password'])) {
            $_SESSION['hospital_logged_in'] = true;
            $_SESSION['hospital_id'] = $hospital['id'];
            $_SESSION['hospital_name'] = $hospital['hospital_name'];
            $_SESSION['hospital_email'] = $hospital['email'];
            header("Location: hospital_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Hospital not found!";
    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $hospital_name = trim($_POST['hospital_name']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $registration_number = trim($_POST['registration_number']);
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT id FROM hospitals WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        if ($checkEmail->get_result()->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO hospitals (hospital_name, email, password, phone, address, city, registration_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $hospital_name, $email, $hashed_password, $phone, $address, $city, $registration_number);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Portal | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #d90429 0%, #ef233c 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: bold;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .nav-tabs .nav-link {
            color: #666;
            font-weight: 600;
        }
        
        .nav-tabs .nav-link.active {
            color: #d90429;
            border-bottom: 3px solid #d90429;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #d90429 0%, #ef233c 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(217, 4, 41, 0.3);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <i class="bi bi-hospital fs-1"></i>
        <h2 class="mt-2">Hospital Portal</h2>
        <p class="mb-0">RedTrack Blood Management System</p>
    </div>
    
    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">
                    <i class="bi bi-person-plus"></i> Register
                </button>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="login">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="hospital@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Dashboard
                    </button>
                </form>
            </div>
            
            <!-- Registration Form -->
            <div class="tab-pane fade" id="register">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-hospital"></i> Hospital Name</label>
                            <input type="text" name="hospital_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" name="reg_email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                            <input type="password" name="reg_password" class="form-control" minlength="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-telephone"></i> Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-geo-alt"></i> City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-house"></i> Address</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-text"></i> Registration Number</label>
                        <input type="text" name="registration_number" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus"></i> Register Hospital
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>