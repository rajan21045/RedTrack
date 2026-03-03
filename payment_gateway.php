<?php
session_start();

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if transaction exists
if (!isset($_SESSION['pending_transaction'])) {
    header("Location: adminpanel.php");
    exit();
}

$transaction = $_SESSION['pending_transaction'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-header i {
            font-size: 60px;
            color: #d90429;
            margin-bottom: 15px;
        }
        
        .transaction-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2rem;
            color: #d90429;
        }
        
        .transport-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .payment-methods {
            margin-bottom: 25px;
        }
        
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .payment-option:hover {
            border-color: #d90429;
            background: #fff5f5;
        }
        
        .payment-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .payment-option.selected {
            border-color: #d90429;
            background: #fff5f5;
        }
        
        .btn-pay {
            background: linear-gradient(135deg, #d90429 0%, #ef233c 100%);
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: bold;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(217, 4, 41, 0.3);
        }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="payment-header">
        <i class="bi bi-credit-card-2-front"></i>
        <h3 class="fw-bold">Payment Gateway</h3>
        <p class="text-muted">Secure Blood Stock Transfer Payment</p>
    </div>
    
    <div class="transaction-details">
        <h5 class="mb-3"><i class="bi bi-receipt"></i> Transaction Details</h5>
        <div class="detail-row">
            <span>Hospital Name:</span>
            <strong><?= htmlspecialchars($transaction['hospital_name']) ?></strong>
        </div>
        <div class="detail-row">
            <span>Blood Group:</span>
            <strong class="text-danger"><?= htmlspecialchars($transaction['blood_group']) ?></strong>
        </div>
        <div class="detail-row">
            <span>Units:</span>
            <strong><?= $transaction['units'] ?> Units</strong>
        </div>
        <div class="detail-row">
            <span>Transport Partner:</span>
            <span class="transport-badge">
                <i class="bi bi-truck"></i> <?= htmlspecialchars($transaction['transport_partner']) ?>
            </span>
        </div>
        <div class="detail-row">
            <span>Price per Unit:</span>
            <strong>₹500</strong>
        </div>
        <div class="detail-row">
            <span>Total Amount:</span>
            <strong>₹<?= number_format($transaction['total_amount'], 2) ?></strong>
        </div>
    </div>
    
    <form action="process_payment.php" method="POST" id="paymentForm">
        <div class="payment-methods">
            <h6 class="mb-3"><i class="bi bi-wallet2"></i> Select Payment Method</h6>
            
            <label class="payment-option" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="card" required>
                <i class="bi bi-credit-card fs-4 text-primary"></i>
                <div>
                    <strong>Credit/Debit Card</strong>
                    <br><small class="text-muted">Visa, MasterCard, RuPay</small>
                </div>
            </label>
            
            <label class="payment-option" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="upi" required>
                <i class="bi bi-phone fs-4 text-success"></i>
                <div>
                    <strong>UPI Payment</strong>
                    <br><small class="text-muted">Google Pay, PhonePe, Paytm</small>
                </div>
            </label>
            
            <label class="payment-option" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="netbanking" required>
                <i class="bi bi-bank fs-4 text-info"></i>
                <div>
                    <strong>Net Banking</strong>
                    <br><small class="text-muted">All major banks</small>
                </div>
            </label>
            
            <label class="payment-option" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="wallet" required>
                <i class="bi bi-wallet fs-4 text-warning"></i>
                <div>
                    <strong>Digital Wallet</strong>
                    <br><small class="text-muted">Paytm, PhonePe Wallet</small>
                </div>
            </label>
        </div>
        
        <button type="submit" class="btn btn-danger btn-pay">
            <i class="bi bi-shield-check"></i> Pay ₹<?= number_format($transaction['total_amount'], 2) ?>
        </button>
        
        <a href="cancel_payment.php" class="btn btn-outline-secondary w-100 mt-3">Cancel Transaction</a>
    </form>
    
    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="bi bi-lock-fill"></i> Secured by 256-bit SSL encryption
        </small>
    </div>
</div>

<script>
    function selectPayment(element) {
        // Remove selected class from all options
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        // Add selected class to clicked option
        element.classList.add('selected');
    }
</script>

</body>
</html>