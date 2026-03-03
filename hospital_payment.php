<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    header("Location: hospital_login.php");
    exit();
}

// Check if transaction exists
if (!isset($_SESSION['pending_hospital_transaction'])) {
    header("Location: hospital_dashboard.php");
    exit();
}

$transaction = $_SESSION['pending_hospital_transaction'];

// Generate unique transaction ID
$unique_transaction_id = 'BLOOD_' . time() . '_' . $transaction['hospital_id'];
$_SESSION['unique_transaction_id'] = $unique_transaction_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway | Hospital Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Khalti Checkout -->
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-header i {
            font-size: 60px;
            color: #667eea;
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
            color: #667eea;
        }
        
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .payment-option:hover {
            border-color: #667eea;
            background: #f5f7ff;
            transform: translateY(-2px);
        }
        
        .payment-option.selected {
            border-color: #667eea;
            background: #f5f7ff;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .payment-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .urgency-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .urgency-normal { background: #d1ecf1; color: #0c5460; }
        .urgency-urgent { background: #fff3cd; color: #856404; }
        .urgency-critical { background: #f8d7da; color: #721c24; }

        .btn-khalti {
            background: #5C2D91;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-khalti:hover {
            background: #4a2373;
            transform: translateY(-2px);
        }

        .btn-esewa {
            background: #60BB46;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-esewa:hover {
            background: #4d9637;
            transform: translateY(-2px);
        }

        .btn-ime {
            background: #ED1C24;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-ime:hover {
            background: #c91219;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="payment-header">
        <i class="bi bi-credit-card-2-front"></i>
        <h3 class="fw-bold">Payment Gateway</h3>
        <p class="text-muted">Secure Blood Request Payment</p>
    </div>
    
    <div class="transaction-details">
        <h5 class="mb-3"><i class="bi bi-receipt"></i> Request Details</h5>
        <div class="detail-row">
            <span>Hospital:</span>
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
            <span>Urgency:</span>
            <span class="urgency-badge urgency-<?= $transaction['urgency'] ?>">
                <?= strtoupper($transaction['urgency']) ?>
            </span>
        </div>
        <?php if (!empty($transaction['notes'])): ?>
        <div class="detail-row">
            <span>Notes:</span>
            <small class="text-muted"><?= htmlspecialchars($transaction['notes']) ?></small>
        </div>
        <?php endif; ?>
        <div class="detail-row">
            <span>Price per Unit:</span>
            <strong>NPR 500</strong>
        </div>
        <div class="detail-row">
            <span>Total Amount:</span>
            <strong>NPR <?= number_format($transaction['total_amount'], 2) ?></strong>
        </div>
    </div>
    
    <div class="payment-methods mb-4">
        <h6 class="mb-3"><i class="bi bi-wallet2"></i> Choose Payment Method</h6>
        
        <!-- Khalti Payment -->
        <div class="payment-option" id="khalti-option">
            <img src="https://play-lh.googleusercontent.com/XEZFD-Ba32qVaLefibNch83m1JN8s1VPPOmi4JCdz9sZPJgEBv_MbK_ylJBfmZRb8w" 
                 alt="Khalti" class="payment-logo">
            <div class="flex-grow-1">
                <strong>Khalti Wallet</strong>
                <br><small class="text-muted">Pay with Khalti Digital Wallet</small>
            </div>
            <button type="button" class="btn btn-khalti" id="payment-button">
                <i class="bi bi-wallet2"></i> Pay with Khalti
            </button>
        </div>
        
        <!-- eSewa Payment -->
        <div class="payment-option" id="esewa-option">
            <img src="https://play-lh.googleusercontent.com/MRzMmiJAe0x7contrfv5pLxALVMOx2I00es3yABU8ZJDcNXdk-d0MhWpS0ti1NN_V-w=w240-h480-rw" 
                 alt="eSewa" class="payment-logo">
            <div class="flex-grow-1">
                <strong>eSewa</strong>
                <br><small class="text-muted">Pay with eSewa Wallet</small>
            </div>
            <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
                <input type="hidden" name="amount" value="<?= $transaction['total_amount'] ?>">
                <input type="hidden" name="tax_amount" value="0">
                <input type="hidden" name="total_amount" value="<?= $transaction['total_amount'] ?>">
                <input type="hidden" name="transaction_uuid" value="<?= $unique_transaction_id ?>">
                <input type="hidden" name="product_code" value="EPAYTEST">
                <input type="hidden" name="product_service_charge" value="0">
                <input type="hidden" name="product_delivery_charge" value="0">
                <input type="hidden" name="success_url" value="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/esewa_success.php' ?>">
                <input type="hidden" name="failure_url" value="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/esewa_failure.php' ?>">
                <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                <input type="hidden" name="signature" value="<?= base64_encode(hash_hmac('sha256', "total_amount={$transaction['total_amount']},transaction_uuid={$unique_transaction_id},product_code=EPAYTEST", '8gBm/:&flhVO0xqPa(nSOlQnpJUWXI', true)) ?>">
                <button type="submit" class="btn btn-esewa">
                    <i class="bi bi-wallet2"></i> Pay with eSewa
                </button>
            </form>
        </div>
        
        <!-- IME Pay -->
        <div class="payment-option" id="ime-option">
            <img src="https://play-lh.googleusercontent.com/1BKpatcoS74CCPOjKLVoRRbfNBCwRPKQ5yCPcEZ4yQZHaxJhLpFp0r-b7cOxixTiuFU=w240-h480-rw" 
                 alt="IME Pay" class="payment-logo">
            <div class="flex-grow-1">
                <strong>IME Pay</strong>
                <br><small class="text-muted">Pay with IME Pay Wallet</small>
            </div>
            <button type="button" class="btn btn-ime" onclick="imePayment()">
                <i class="bi bi-wallet2"></i> Pay with IME Pay
            </button>
        </div>
    </div>
    
    <a href="hospital_cancel_payment.php" class="btn btn-outline-secondary w-100">
        <i class="bi bi-x-circle"></i> Cancel Request
    </a>
    
    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="bi bi-lock-fill"></i> All payments are secured and encrypted
        </small>
    </div>
</div>

<script>
    // Khalti Payment Configuration
    var config = {
        // Replace with your Khalti public key
        "publicKey": "test_public_key_dc74e0fd57cb46cd93832aee0a390234",
        "productIdentity": "<?= $unique_transaction_id ?>",
        "productName": "Blood Request - <?= $transaction['blood_group'] ?> (<?= $transaction['units'] ?> units)",
        "productUrl": "<?= 'http://' . $_SERVER['HTTP_HOST'] ?>",
        "paymentPreference": [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT"
        ],
        "eventHandler": {
            onSuccess(payload) {
                console.log(payload);
                // Send verification request to server
                verifyKhaltiPayment(payload);
            },
            onError(error) {
                console.log(error);
                alert('Payment failed: ' + error.message);
            },
            onClose() {
                console.log('Payment widget closed');
            }
        }
    };

    var checkout = new KhaltiCheckout(config);
    var btn = document.getElementById("payment-button");
    
    btn.onclick = function() {
        // Amount in paisa (multiply by 100)
        var amountInPaisa = <?= $transaction['total_amount'] ?> * 100;
        checkout.show({amount: amountInPaisa});
    }

    function verifyKhaltiPayment(payload) {
        // Show loading
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Verifying...';
        btn.disabled = true;

        // Send to verification endpoint
        fetch('khalti_verify.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'hospital_payment_success.php';
            } else {
                alert('Payment verification failed: ' + data.message);
                btn.innerHTML = '<i class="bi bi-wallet2"></i> Pay with Khalti';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Payment verification failed. Please contact support.');
            btn.innerHTML = '<i class="bi bi-wallet2"></i> Pay with Khalti';
            btn.disabled = false;
        });
    }

    function imePayment() {
        alert('IME Pay integration coming soon! Please use Khalti or eSewa.');
    }

    // Highlight selected payment option
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });
</script>

</body>
</html>