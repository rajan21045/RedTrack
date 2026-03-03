<?php
$connDonor = new mysqli("localhost", "root", "*rajan12345#", "list_donor");
$connUsers = new mysqli("localhost", "root", "*rajan12345#", "store_data_login_signup");

$type = $_GET['type'] ?? ''; 
$id = intval($_GET['id'] ?? 0);
$message = "";

if (isset($_POST['update_now'])) {
    if ($type == 'user') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        if (!empty($_POST['new_password'])) {
            $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $connUsers->query("UPDATE sign_up SET username='$username', email='$email', password_hash='$hashed' WHERE id=$id");
        } else {
            $connUsers->query("UPDATE sign_up SET username='$username', email='$email' WHERE id=$id");
        }
        $message = "User updated successfully!";
    } elseif ($type == 'donor') {
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $bg = $_POST['blood_group'];
        $connDonor->query("UPDATE donors SET fullname='$fullname', phone='$phone', blood_group='$bg' WHERE donor_id=$id");
        $message = "Donor updated successfully!";
    }
}

if ($type == 'user') {
    $data = $connUsers->query("SELECT * FROM sign_up WHERE id=$id")->fetch_assoc();
} else {
    $data = $connDonor->query("SELECT * FROM donors WHERE donor_id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit <?php echo ucfirst($type); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f0f2f5; display: flex; align-items: center; min-height: 100vh; }
        .edit-card { border:none; border-radius:15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 450px; margin: auto; background: #fff; }
        .btn-primary-custom { background: #c70039; color: #fff; border: none; padding: 10px; border-radius: 8px; }
        .btn-primary-custom:hover { background: #a0002e; color: #fff; }
    </style>
</head>
<body>

<div class="edit-card p-4">
    <h4 class="text-center mb-4 text-danger fw-bold">Update <?php echo ucfirst($type); ?></h4>
    
    <?php if($message): ?>
        <div class="alert alert-success border-0 shadow-sm text-center"><?php echo $message; ?></div>
        <a href="adminpanel.php" class="btn btn-dark w-100">Back to Panel</a>
    <?php else: ?>
        <form method="POST">
            <?php if ($type == 'user'): ?>
                <div class="mb-3"><label class="form-label small fw-bold text-muted">USERNAME</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" required></div>
                
                <div class="mb-3"><label class="form-label small fw-bold text-muted">EMAIL</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required></div>
                
                <div class="mb-3"><label class="form-label small fw-bold text-muted">NEW PASSWORD (LEAVE BLANK TO KEEP)</label>
                <input type="password" name="new_password" class="form-control"></div>
            <?php else: ?>
                <div class="mb-3"><label class="form-label small fw-bold text-muted">FULL NAME</label>
                <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($data['fullname']); ?>" required></div>
                                <div class="mb-3"><label class="form-label small fw-bold text-muted">PHONE</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($data['phone']); ?>" required></div>
                
                <div class="mb-3"><label class="form-label small fw-bold text-muted">BLOOD GROUP</label>
                <select name="blood_group" class="form-select">
                    <?php foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $g): ?>
                        <option value="<?php echo $g; ?>" <?php if($data['blood_group']==$g) echo 'selected'; ?>><?php echo $g; ?></option>
                    <?php endforeach; ?>
                </select></div>
            <?php endif; ?>
            <button type="submit" name="update_now" class="btn btn-primary-custom w-100 mt-3">Save Changes</button>
            <a href="adminpanel.php" class="btn btn-light w-100 mt-2 border">Cancel</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>