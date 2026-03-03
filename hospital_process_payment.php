<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    header("Location: hospital_login.php");
    exit();
}

// Check if transaction exists
if (!isset($_SESSION['pending_hospital_transaction']) || !isset($_POST['payment_method'])) {
    header("Location: hospital_dashboard.php");
    exit();
}

$transaction = $_SESSION['pending_hospital_transaction'];
$payment_method = $_POST['payment_method'];

// Database connection
$conn = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Begin transaction
$conn->begin_transaction();

try {
    // Deduct blood from inventory
    $updateInventory = $conn->prepare("UPDATE blood_inventory SET units = units - ? WHERE blood_group = ?");
    $updateInventory->bind_param("is", $transaction['units'], $transaction['blood_group']);
    $updateInventory->execute();
    
    // Insert transaction record
    $insertTransaction = $conn->prepare("INSERT INTO hospital_transactions (hospital_name, blood_group, units, amount, payment_method, status) VALUES (?, ?, ?, ?, ?, 'completed')");
    $insertTransaction->bind_param("ssids", 
        $transaction['hospital_name'], 
        $transaction['blood_group'], 
        $transaction['units'], 
        $transaction['total_amount'], 
        $payment_method
    );
    $insertTransaction->execute();
    $transaction_id = $conn->insert_id;
    
    // Update blood request status
    $updateRequest = $conn->prepare("UPDATE blood_requests SET status = 'approved' WHERE hospital_id = ? AND blood_group = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
    $updateRequest->bind_param("is", $transaction['hospital_id'], $transaction['blood_group']);
    $updateRequest->execute();
    
    // Commit transaction
    $conn->commit();
    
    // Clear pending transaction
    unset($_SESSION['pending_hospital_transaction']);
    
    // Set success message
    $_SESSION['payment_success'] = [
        'transaction_id' => $transaction_id,
        'hospital_name' => $transaction['hospital_name'],
        'blood_group' => $transaction['blood_group'],
        'units' => $transaction['units'],
        'amount' => $transaction['total_amount'],
        'payment_method' => $payment_method,
        'urgency' => $transaction['urgency']
    ];
    
    // Redirect to success page
    header("Location: hospital_payment_success.php");
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['error'] = "Payment failed: " . $e->getMessage();
    header("Location: hospital_dashboard.php");
    exit();
}

$conn->close();
?>