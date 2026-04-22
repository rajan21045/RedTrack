<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Collect POST data
$h_name            = $_POST['h_name'];
$b_group           = $_POST['b_group'];
$units             = $_POST['units'];
$transport_partner = $_POST['transport_partner'];
$payment_method    = $_POST['payment_method'];

// Database connection
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($connInventory->connect_error) {
    die("Connection failed: " . $connInventory->connect_error);
}

// Price calculation
$price_per_unit = 500;
$total_amount   = $units * $price_per_unit;

// Unique payment token
$payment_token = uniqid('RT_', true);

/* =================================================
   CASE 1: CASH ON DELIVERY
================================================= */
if ($payment_method == "COD") {

    $status = "Pending";

    $stmt = $connInventory->prepare("
        INSERT INTO hospital_transactions
        (hospital_name, blood_group, units, transport_partner, amount, payment_method, payment_token, status, transaction_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "ssisdsss",
        $h_name,
        $b_group,
        $units,
        $transport_partner,
        $total_amount,
        $payment_method,
        $payment_token,
        $status
    );

    $stmt->execute();
    $stmt->close();

    // Deduct inventory
    $stmt2 = $connInventory->prepare("
        UPDATE blood_inventory
        SET units = units - ?
        WHERE blood_group = ?
    ");
    $stmt2->bind_param("is", $units, $b_group);
    $stmt2->execute();
    $stmt2->close();

    $connInventory->close();

    $_SESSION['success'] = "Stock sent successfully with COD!";
    header("Location: adminpanel.php");
    exit();
}

/* =================================================
   CASE 2: ESEWA
================================================= */ elseif ($payment_method == "Esewa") {

    $status = "Pending";

    $stmt = $connInventory->prepare("
        INSERT INTO hospital_transactions
        (hospital_name, blood_group, units, transport_partner, amount, payment_method, payment_token, status, transaction_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "ssisdsss",
        $h_name,
        $b_group,
        $units,
        $transport_partner,
        $total_amount,
        $payment_method,
        $payment_token,
        $status
    );

    $stmt->execute();
    $stmt->close();

    // Deduct inventory
    $stmt2 = $connInventory->prepare("
        UPDATE blood_inventory
        SET units = units - ?
        WHERE blood_group = ?
    ");
    $stmt2->bind_param("is", $units, $b_group);
    $stmt2->execute();
    $stmt2->close();

    $connInventory->close();

    // eSewa signature
    $product_code = "EPAYTEST";
    $message      = "total_amount=$total_amount,transaction_uuid=$payment_token,product_code=$product_code";
    $secret       = "8gBm/:&EnhH.1/q";

    $hash      = hash_hmac('sha256', $message, $secret, true);
    $signature = base64_encode($hash);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Redirecting to eSewa...</title>
</head>

<body>

    <form id="esewa_form" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" name="amount" value="<?= $total_amount ?>">
        <input type="hidden" name="tax_amount" value="0">
        <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
        <input type="hidden" name="transaction_uuid" value="<?= $payment_token ?>">
        <input type="hidden" name="product_code" value="EPAYTEST">
        <input type="hidden" name="product_service_charge" value="0">
        <input type="hidden" name="product_delivery_charge" value="0">
        <input type="hidden" name="success_url" value="http://localhost/redtrack/success.php">
        <input type="hidden" name="failure_url" value="http://localhost/redtrack/failure.php">
        <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="hidden" name="signature" value="<?= $signature ?>">
    </form>

    <script>
        document.getElementById("esewa_form").submit();
    </script>

</body>

</html>