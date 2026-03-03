<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$conn_auth = new mysqli("localhost", "root", "*rajan12345#", "store_data_login_signUP");
$conn_donor = new mysqli("localhost", "root", "*rajan12345#", "list_donor");
$conn_chat = new mysqli("localhost", "root", "*rajan12345#", "blood_donation");

$username = $_SESSION['username'];

// Fetch Profile Info
$stmt = $conn_auth->prepare("SELECT email, id FROM sign_up WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$user_id = $user_data['id'];
$email = $user_data['email'];

// Fetch Donor Stats
$donor_stmt = $conn_donor->prepare("SELECT * FROM donors WHERE email = ?");
$donor_stmt->bind_param("s", $email);
$donor_stmt->execute();
$donor = $donor_stmt->get_result()->fetch_assoc();

/**
 * UPDATED CHAT QUERY
 * Now joins with the sign_up table to get the real username of the other person.
 */
$chat_q = $conn_chat->prepare("
    SELECT 
        c.id, 
        CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END as other_id, 
        m.message, 
        m.created_at,
        u.username as other_username
    FROM conversations c 
    JOIN messages m ON m.conversation_id = c.id 
    JOIN store_data_login_signUP.sign_up u ON (CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END) = u.id
    WHERE (c.user1_id = ? OR c.user2_id = ?) 
    AND m.id = (SELECT MAX(id) FROM messages WHERE conversation_id = c.id) 
    ORDER BY m.created_at DESC LIMIT 3");

$chat_q->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$chat_q->execute();
$recent_chats = $chat_q->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('img/myaccount-bg.jpg') center/cover;
            z-index: -1;
        }

        .glass-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.2);
            padding: 35px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .profile-pic {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: #dc3545;
            margin: 0 auto 15px;
        }

        .chat-row {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 10px 15px;
            margin-top: 10px;
            transition: 0.3s;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: white;
        }

        .chat-row:hover {
            background: rgba(0, 0, 0, 0.4);
            transform: translateX(8px);
            color: #ffc107;
        }

        .btn-update-profile {
            background: #d90429;
            border: none;
            color: white;
            transition: 0.3s;
        }

        .btn-update-profile:hover {
            background: #ef233c;
            color: white;
            transform: scale(1.02);
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
                    <a class="nav-link mx-3 active text-danger fw-bold" href="#">My Account</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg"></div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="glass-card text-center">
                    <div class="profile-pic"><i class="bi bi-person-fill"></i></div>
                    <h3 class="fw-bold mb-0"><?= $username ?></h3>
                    <p class="small opacity-75 mb-4"><?= $email ?></p>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="p-2 border rounded border-light small">Group: <b><?= $donor['blood_group'] ?? 'N/A' ?></b></div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded border-light small">Status: <b><?= $donor['status'] ?? 'User' ?></b></div>
                        </div>
                    </div>

                    <h6 class="text-start mt-4 mb-2 small fw-bold text-uppercase"><i class="bi bi-chat-dots-fill me-2"></i>Recent Conversations</h6>
                    <?php if ($recent_chats->num_rows > 0): ?>
                        <?php while ($c = $recent_chats->fetch_assoc()): ?>
                            <a href="chat.php?receiver_id=<?= $c['other_id'] ?>" class="chat-row text-start">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small text-info"><i class="bi bi-person me-1"></i><?= htmlspecialchars($c['other_username']) ?></span>
                                    <span style="font-size: 10px; opacity: 0.6;"><?= date('M d, H:i', strtotime($c['created_at'])) ?></span>
                                </div>
                                <div class="small text-truncate opacity-75"><?= htmlspecialchars($m_text = $c['message']) ?></div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="small opacity-50 py-3">No messages yet.</p>
                    <?php endif; ?>

                    <hr class="border-light my-4">

                    <div class="d-grid gap-2">
                        <a href="updateprofile.php" class="btn btn-update-profile fw-bold"><i class="bi bi-pencil-square me-2"></i>Update Profile</a>

                        <a href="chat1.php" class="btn btn-primary fw-bold">Message Admin</a> <a href="changepassword.php" class="btn btn-warning fw-bold">Change Password</a>
                        <a href="logout.php" class="btn btn-danger fw-bold">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>