<?php
session_start();
$connSupport = new mysqli("localhost", "root", "*rajan12345#", "admin_support");
$uid = intval($_GET['user_id']);

if (isset($_POST['reply'])) {
    $msg = $connSupport->real_escape_string($_POST['msg']);
    $u_data = $connSupport->query("SELECT username FROM support_messages WHERE user_id = $uid LIMIT 1")->fetch_assoc();
    $uname = $u_data['username'];
    $connSupport->query("INSERT INTO support_messages (user_id, username, sender_type, message) VALUES ($uid, '$uname', 'admin', '$msg')");
    header("Location: admin_reply.php?user_id=$uid"); exit();
}
$chat = $connSupport->query("SELECT * FROM support_messages WHERE user_id = $uid ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Admin Reply</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-box { height: 400px; overflow-y: auto; background: #fdfdfd; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .bubble { padding: 10px; border-radius: 10px; margin-bottom: 10px; max-width: 80%; }
        .admin { background: #007bff; color: white; margin-left: auto; }
        .user { background: #f1f1f1; color: black; }
    </style>
</head>
<body class="p-5">
    <div class="container" style="max-width: 700px;">
        <h3>Support Thread: User #<?= $uid ?></h3>
        <div class="chat-box" id="c">
            <?php while($m = $chat->fetch_assoc()): ?>
                <div class="bubble <?= $m['sender_type'] ?>"><?= $m['message'] ?></div>
            <?php endwhile; ?>
        </div>
        <form method="POST" class="mt-3 input-group">
            <input name="msg" class="form-control" placeholder="Write reply..." required>
            <button name="reply" class="btn btn-primary">Send Reply</button>
            <a href="adminpanel.php" class="btn btn-secondary">Close</a>
        </form>
    </div>
    <script>var c = document.getElementById("c"); c.scrollTop = c.scrollHeight;</script>
</body>
</html>