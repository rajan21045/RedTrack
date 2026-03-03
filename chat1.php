<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: index.php"); exit(); }

$conn_support = new mysqli("localhost", "root", "*rajan12345#", "admin_support");
$user_id = $_SESSION['user_id']; 
$username = $_SESSION['username'];

if (isset($_POST['send_support'])) {
    $msg = $conn_support->real_escape_string(trim($_POST['message']));
    if (!empty($msg)) {
        $conn_support->query("INSERT INTO support_messages (user_id, username, sender_type, message) 
                             VALUES ($user_id, '$username', 'user', '$msg')");
        header("Location: chat1.php"); exit();
    }
}
$history = $conn_support->query("SELECT * FROM support_messages WHERE user_id = $user_id ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Support Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-window { height: 400px; overflow-y: auto; background: #fff; border-radius: 15px; padding: 20px; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); }
        .bubble { margin-bottom: 15px; padding: 10px 15px; border-radius: 15px; max-width: 80%; clear: both; }
        .user-msg { background: #d90429; color: white; float: right; border-bottom-right-radius: 2px; }
        .admin-msg { background: #e9ecef; color: black; float: left; border-bottom-left-radius: 2px; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 600px;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <span><i class="bi bi-headset me-2"></i>Admin Support</span>
                <a href="myaccount.php" class="btn btn-sm btn-outline-light">Back</a>
            </div>
            <div class="card-body">
                <div class="chat-window" id="win">
                    <?php while($m = $history->fetch_assoc()): ?>
                        <div class="bubble <?= $m['sender_type'] == 'user' ? 'user-msg' : 'admin-msg' ?>">
                            <?= htmlspecialchars($m['message']) ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <form method="POST" class="mt-3">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type message..." required>
                        <button class="btn btn-danger" name="send_support">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>var w = document.getElementById("win"); w.scrollTop = w.scrollHeight;</script>
</body>
</html>