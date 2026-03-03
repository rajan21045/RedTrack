<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);

// DB Connections
$db_chat = new mysqli("localhost", "root", "*rajan12345#", "blood_donation");
$db_donor = new mysqli("localhost", "root", "*rajan12345#", "list_donor");

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<div class='container mt-5 alert alert-danger'>Please login first to chat with donors.</div>");
}

$current_user = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? null;

if (!$receiver_id) { die("Invalid User."); }

// 2. Get Receiver (Donor) Name from the other database
$stmt_name = $db_donor->prepare("SELECT fullname FROM donors WHERE donor_id = ?");
$stmt_name->bind_param("i", $receiver_id);
$stmt_name->execute();
$res_name = $stmt_name->get_result();
$receiver_data = $res_name->fetch_assoc();
$receiver_name = $receiver_data['fullname'] ?? "Donor";

// 3. Find or Create Conversation ID
$stmt_conv = $db_chat->prepare("SELECT id FROM conversations WHERE 
    (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
$stmt_conv->bind_param("iiii", $current_user, $receiver_id, $receiver_id, $current_user);
$stmt_conv->execute();
$res_conv = $stmt_conv->get_result();

if ($res_conv->num_rows > 0) {
    $conv = $res_conv->fetch_assoc();
    $conversation_id = $conv['id'];
} else {
    $stmt_new = $db_chat->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)");
    $stmt_new->bind_param("ii", $current_user, $receiver_id);
    $stmt_new->execute();
    $conversation_id = $db_chat->insert_id;
}

// 4. Handle Sending Message
if (isset($_POST['send_msg']) && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $stmt_send = $db_chat->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
    $stmt_send->bind_param("iis", $conversation_id, $current_user, $msg);
    $stmt_send->execute();
    header("Location: chat.php?receiver_id=$receiver_id"); // Refresh to clear post
    exit();
}

// 5. Fetch Messages
$messages = $db_chat->query("SELECT * FROM messages WHERE conversation_id = $conversation_id ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $receiver_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #e5ddd5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .chat-container { max-width: 600px; margin: 20px auto; background: #fff; height: 90vh; display: flex; flex-direction: column; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
        .chat-header { background: #075e54; color: white; padding: 15px; display: flex; align-items: center; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: contain; }
        .message-bubble { max-width: 75%; padding: 8px 12px; border-radius: 8px; margin-bottom: 10px; position: relative; font-size: 15px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
        .sent { background: #dcf8c6; align-self: flex-end; margin-left: auto; border-top-right-radius: 0; }
        .received { background: #fff; align-self: flex-start; border-top-left-radius: 0; }
        .msg-time { font-size: 10px; color: #888; text-align: right; margin-top: 4px; }
        .chat-footer { background: #f0f0f0; padding: 10px; }
        .input-group { background: white; border-radius: 25px; overflow: hidden; padding-left: 15px; }
        .input-group input { border: none; padding: 10px; box-shadow: none !important; }
        .btn-send { background: #075e54; color: white; border-radius: 50%; width: 45px; height: 45px; border: none; margin-left: 10px; transition: 0.3s; }
        .btn-send:hover { background: #128c7e; transform: scale(1.1); }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <a href="searchdonor.php" class="text-white me-3"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <h6 class="mb-0"><?php echo htmlspecialchars($receiver_name); ?></h6>
            <small style="font-size: 11px; opacity: 0.8;">Donor</small>
        </div>
    </div>

    <div class="chat-messages d-flex flex-column" id="chatBox">
        <?php if ($messages->num_rows == 0): ?>
            <div class="text-center mt-5 text-muted small bg-white p-2 rounded shadow-sm align-self-center">
                No messages yet. Start the conversation!
            </div>
        <?php endif; ?>

        <?php while($m = $messages->fetch_assoc()): ?>
            <div class="message-bubble <?php echo ($m['sender_id'] == $current_user) ? 'sent' : 'received'; ?>">
                <?php echo htmlspecialchars($m['message']); ?>
                <div class="msg-time"><?php echo date('H:i', strtotime($m['created_at'])); ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="chat-footer">
        <form method="POST" class="d-flex align-items-center">
            <div class="input-group flex-grow-1">
                <input type="text" name="message" class="form-control" placeholder="Type a message..." required autocomplete="off">
            </div>
            <button type="submit" name="send_msg" class="btn-send">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto-scroll to bottom of chat
    var chatBox = document.getElementById("chatBox");
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>