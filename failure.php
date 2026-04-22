<?php
$transaction_id = isset($_GET['oid']) ? $_GET['oid'] : 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Failed | RedTrack</title>
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
            background: #f8d7da;
            color: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .btn-outline-secondary {
            padding: 12px 30px;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="card p-5">
        <div class="icon-circle">
            <i class="bi bi-x-lg"></i>
        </div>
        <h2 class="fw-bold text-dark">Payment Failed</h2>
        <p class="text-muted">
            We're sorry, but the payment for Transaction <strong>#<?= htmlspecialchars($transaction_id) ?></strong> could not be processed at this time.
        </p>

        <div class="alert alert-warning text-start">
            <small><strong>Possible reasons:</strong></small>
            <ul class="mb-0 small">
                <li>Insufficient balance in the account.</li>
                <li>Connection timeout with the bank.</li>
                <li>Transaction cancelled by the user.</li>
            </ul>
        </div>

        <div class="mt-4 d-grid gap-2">
            <a href="admin_dashboard.php" class="btn btn-danger py-2">Try Again</a>
            <a href="support.php" class="btn btn-outline-secondary py-2">Contact Support</a>
        </div>
    </div>

</body>

</html>