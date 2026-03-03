<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost:3306", "root", "*rajan12345#", "store_data_login_signUP");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

if (isset($_POST['change'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {

        $stmt = $conn->prepare(
            "SELECT password_hash FROM sign_up WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($old_password, $user['password_hash'])) {

            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare(
                "UPDATE sign_up SET password_hash = ? WHERE username = ?"
            );
            $update->bind_param("ss", $new_hash, $username);

            if ($update->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Failed to update password.";
            }

        } else {
            $error = "Old password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - RedTrack</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('img/myaccount-bg.jpg');
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        .glass-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.25);
        }

        .profile-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 45px;
            color: #dc3545;
            margin: auto;
        }

        label {
            color: #fff;
            font-weight: 600;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">RedTrack</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                <a class="nav-link mx-3" href="homepage.php">Home</a>
                <a class="nav-link mx-3 active text-danger fw-bold" href="#">Change Password</a>
            </div>
        </div>
    </div>
</nav>

<!-- BACKGROUND -->
<div class="bg"></div>

<!-- CHANGE PASSWORD CARD -->
<div class="container d-flex justify-content-center align-items-center" style="height: 90vh;">
    <div class="glass-card col-md-6 text-center">

        <div class="profile-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h2 class="mt-3 text-white">Change Password</h2>
        <hr class="border-light">

        <?php if (isset($success)) : ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label>Old Password</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>

            <div class="mb-3 text-start">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="mb-4 text-start">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="d-grid gap-3">
                <button name="change" class="btn btn-warning fw-bold py-2">
                    Update Password
                </button>

                <a href="myaccount.php" class="btn btn-danger fw-bold py-2">
                    Back to My Account
                </a>
            </div>
        </form>

    </div>
</div>

<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
