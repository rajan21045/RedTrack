<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    header("Location: hospital_login.php");
    exit();
}

// Check if payment success data exists
if (!isset($_SESSION['payment_success'])) {
    header("Location: hospital_dashboard.php");
    exit();
}

$payment = $_SESSION['payment_success'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Hospital Portal</title>
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

        .payment-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .payment-khalti {
            background: #5C2D91;
            color: white;
        }

        .payment-esewa {
            background: #60BB46;
            color: white;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="success-icon">
        <i class="bi bi-check-lg"></i>
    </div>
    
    <h2 class="fw-bold text-success mb-3">Payment Successful!</h2>
    <p class="text-muted mb-4">Your blood request has been processed successfully</p>
    
    <div class="transaction-info">
        <h5 class="mb-3"><i class="bi bi-receipt-cutoff"></i> Transaction Receipt</h5>
        <div class="info-row">
            <span>Transaction ID:</span>
            <strong>#<?= $payment['transaction_id'] ?></strong>
        </div>
        <div class="info-row">
            <span>Hospital Name:</span>
            <strong><?= htmlspecialchars($payment['hospital_name']) ?></strong>
        </div>
        <div class="info-row">
            <span>Blood Group:</span>
            <strong class="text-danger"><?= $payment['blood_group'] ?></strong>
        </div>
        <div class="info-row">
            <span>Units Ordered:</span>
            <strong><?= $payment['units'] ?> Units</strong>
        </div>
        <div class="info-row">
            <span>Amount Paid:</span>
            <strong class="text-success">NPR <?= number_format($payment['amount'], 2) ?></strong>
        </div>
        <div class="info-row">
            <span>Payment Method:</span>
            <span class="payment-badge payment-<?= $payment['payment_method'] ?>">
                <?= strtoupper($payment['payment_method']) ?>
            </span>
        </div>
        <?php if (isset($payment['payment_token'])): ?>
        <div class="info-row">
            <span>Payment Token:</span>
            <small class="text-muted"><?= $payment['payment_token'] ?></small>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span>Urgency Level:</span>
            <strong class="text-uppercase"><?= $payment['urgency'] ?></strong>
        </div>
        <div class="info-row">
            <span>Date & Time:</span>
            <strong><?= date('d M Y, h:i A') ?></strong>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> The blood bank will process your request and contact you shortly for delivery arrangements.
    </div>
    
    <div class="d-grid gap-2">
        <a href="hospital_dashboard.php" class="btn btn-success btn-lg">
            <i class="bi bi-house-door"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Print Receipt
        </button>
    </div>
</div>

<?php
// Clear payment success data after displaying
unset($_SESSION['payment_success']);
?>

</body>
</html>