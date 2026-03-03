<?php
session_start();

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : null;

// Fetch transaction details
if ($transaction_id) {
    $connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
    $stmt = $connInventory->prepare("SELECT * FROM hospital_transactions WHERE id = ?");
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        
        .success-icon i {
            font-size: 50px;
            color: white;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .transaction-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="success-icon">
        <i class="bi bi-check-lg"></i>
    </div>
    
    <h2 class="fw-bold text-success mb-3">Payment Successful!</h2>
    <p class="text-muted mb-4">Your blood stock transfer has been completed successfully</p>
    
    <?php if (isset($transaction)): ?>
    <div class="transaction-info">
        <h5 class="mb-3"><i class="bi bi-receipt-cutoff"></i> Transaction Receipt</h5>
        <div class="info-row">
            <span>Transaction ID:</span>
            <strong>#<?= $transaction['id'] ?></strong>
        </div>
        <div class="info-row">
            <span>Hospital Name:</span>
            <strong><?= htmlspecialchars($transaction['hospital_name']) ?></strong>
        </div>
        <div class="info-row">
            <span>Blood Group:</span>
            <strong class="text-danger"><?= $transaction['blood_group'] ?></strong>
        </div>
        <div class="info-row">
            <span>Units Transferred:</span>
            <strong><?= $transaction['units'] ?> Units</strong>
        </div>
        <div class="info-row">
            <span>Amount Paid:</span>
            <strong class="text-success">₹<?= number_format($transaction['amount'], 2) ?></strong>
        </div>
        <div class="info-row">
            <span>Payment Method:</span>
            <strong class="text-uppercase"><?= $transaction['payment_method'] ?></strong>
        </div>
        <div class="info-row">
            <span>Date & Time:</span>
            <strong><?= date('d M Y, h:i A', strtotime($transaction['transaction_date'])) ?></strong>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="d-grid gap-2">
        <a href="adminpanel.php" class="btn btn-success btn-lg">
            <i class="bi bi-house-door"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Print Receipt
        </button>
    </div>
</div>

</body>
</html>