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

$current_username = $_SESSION['username'];

/* Fetch current user data */
$stmt = $conn->prepare("SELECT username, email FROM sign_up WHERE username = ?");
$stmt->bind_param("s", $current_username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Update profile */
if (isset($_POST['update'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    // Check if username already exists (excluding current user)
    $check = $conn->prepare(
        "SELECT username FROM sign_up WHERE username = ? AND username != ?"
    );
    $check->bind_param("ss", $new_username, $current_username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already exists. Please choose another one.";
    } else {
        $update = $conn->prepare(
            "UPDATE sign_up SET username = ?, email = ? WHERE username = ?"
        );
        $update->bind_param("sss", $new_username, $new_email, $current_username);

        if ($update->execute()) {
            $_SESSION['username'] = $new_username;
            $current_username = $new_username;
            $success = "Profile updated successfully.";
        } else {
            $error = "Profile update failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - RedTrack</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .bg {
            position: fixed;
            inset: 0;
            background: url('img/myaccount-bg.jpg') center/cover no-repeat;
            z-index: -1;
        }

        .glass-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .profile-icon {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 50px;
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
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link mx-3" href="homepage.php">Home</a>
                <a class="nav-link mx-3 active text-danger fw-bold" href="#">Update Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="bg"></div>

<!-- UPDATE PROFILE CARD -->
<div class="container d-flex justify-content-center align-items-center" style="height: 90vh;">
    <div class="glass-card col-md-6 text-center">

        <div class="profile-icon">
            <i class="bi bi-person-gear"></i>
        </div>

        <h2 class="mt-3 text-white">Update Profile</h2>
        <hr class="border-light">

        <?php if (isset($success)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label>Username</label>
                <input type="text" name="username" class="form-control"
                       value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="mb-4 text-start">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="d-grid gap-3">
                <button name="update" class="btn btn-light fw-bold py-2">
                    Save Changes
                </button>
                <a href="myaccount.php" class="btn btn-danger fw-bold py-2">
                    Back to My Account
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
