<?php
/* DB CONNECTION */
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

// Check if transaction ID exists (eSewa usually returns 'oid' or 'tid' depending on version)
$transaction_id = isset($_GET['oid']) ? $_GET['oid'] : (isset($_GET['transaction_id']) ? $_GET['transaction_id'] : null);

$updateSuccess = false;

if ($transaction_id) {
    // Update the transaction status in the database
    $stmt = $connInventory->prepare("UPDATE hospital_transactions SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("s", $transaction_id);
    if ($stmt->execute()) {
        $updateSuccess = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Successful | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #d4edda;
            color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .btn-primary {
            background: #ef233c;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="card p-5">
        <div class="icon-circle">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 class="fw-bold text-dark">Payment Successful!</h2>
        <p class="text-muted">
            Thank you. The transaction for Order <strong>#<?= htmlspecialchars($transaction_id) ?></strong> has been completed successfully.
            The blood stock is being prepared for dispatch.
        </p>

        <div class="mt-4">
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

</body>

</html>