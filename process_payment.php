<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if transaction and payment method exist
if (!isset($_SESSION['pending_transaction']) || !isset($_POST['payment_method'])) {
    $_SESSION['error'] = "No pending transaction found!";
    header("Location: adminpanel.php");
    exit();
}

$transaction = $_SESSION['pending_transaction'];
$payment_method = $_POST['payment_method'];

// Database connections
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
$connHospital = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($connInventory->connect_error || $connHospital->connect_error) {
    die("Connection failed: " . $connInventory->connect_error);
}

// Start transaction
$connInventory->begin_transaction();
$connHospital->begin_transaction();

try {
    // 1. Deduct from inventory
    $updateInventory = $connInventory->prepare("UPDATE blood_inventory SET units = units - ? WHERE blood_group = ?");
    $updateInventory->bind_param("is", $transaction['units'], $transaction['blood_group']);
    $updateInventory->execute();
    
    // 2. Insert transaction record with transport partner
    $insertTransaction = $connHospital->prepare("INSERT INTO hospital_transactions 
        (hospital_name, blood_group, units, transport_partner, amount, payment_method, transaction_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 'completed')");
    
    $insertTransaction->bind_param(
        "ssisds", 
        $transaction['hospital_name'], 
        $transaction['blood_group'], 
        $transaction['units'],
        $transaction['transport_partner'],
        $transaction['total_amount'], 
        $payment_method
    );
    
    if (!$insertTransaction->execute()) {
        throw new Exception("Failed to insert transaction: " . $insertTransaction->error);
    }
    
    // Commit both transactions
    $connInventory->commit();
    $connHospital->commit();
    
    // Clear pending transaction
    unset($_SESSION['pending_transaction']);
    
    // Set success message
    $_SESSION['success'] = "Payment successful! Blood stock transferred to " . htmlspecialchars($transaction['hospital_name']) . " via " . htmlspecialchars($transaction['transport_partner']);
    
    header("Location: adminpanel.php");
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $connInventory->rollback();
    $connHospital->rollback();
    
    $_SESSION['error'] = "Payment processing failed: " . $e->getMessage();
    header("Location: payment_gateway.php");
    exit();
}

$connInventory->close();
$connHospital->close();
?>