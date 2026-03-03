<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if transaction exists
if (!isset($_SESSION['pending_hospital_transaction'])) {
    echo json_encode(['success' => false, 'message' => 'No pending transaction']);
    exit();
}

// Get payload from Khalti
$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);

if (!$jsonObj) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$token = $jsonObj->token;
$amount = $jsonObj->amount;

// Khalti verification endpoint
$args = http_build_query([
    'token' => $token,
    'amount' => $amount
]);

// Replace with your Khalti secret key
$secret_key = "test_secret_key_f59e8b7d18b4499ca40f68195a846e9b";

$url = "https://khalti.com/api/v2/payment/verify/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$headers = ['Authorization: Key ' . $secret_key];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Get response
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);

if ($status_code == 200 && isset($response_data['idx'])) {
    // Payment verified successfully
    $transaction = $_SESSION['pending_hospital_transaction'];
    
    // Database connection
    $conn = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Deduct blood from inventory
        $updateInventory = $conn->prepare("UPDATE blood_inventory SET units = units - ? WHERE blood_group = ?");
        $updateInventory->bind_param("is", $transaction['units'], $transaction['blood_group']);
        $updateInventory->execute();
        
        // Insert transaction record
        $insertTransaction = $conn->prepare("INSERT INTO hospital_transactions (hospital_name, blood_group, units, amount, payment_method, status, payment_token) VALUES (?, ?, ?, ?, 'khalti', 'completed', ?)");
        $insertTransaction->bind_param("ssids", 
            $transaction['hospital_name'], 
            $transaction['blood_group'], 
            $transaction['units'], 
            $transaction['total_amount'],
            $response_data['idx']
        );
        $insertTransaction->execute();
        $transaction_id = $conn->insert_id;
        
        // Update blood request status
        $updateRequest = $conn->prepare("UPDATE blood_requests SET status = 'approved' WHERE hospital_id = ? AND blood_group = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
        $updateRequest->bind_param("is", $transaction['hospital_id'], $transaction['blood_group']);
        $updateRequest->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Store success data in session
        $_SESSION['payment_success'] = [
            'transaction_id' => $transaction_id,
            'hospital_name' => $transaction['hospital_name'],
            'blood_group' => $transaction['blood_group'],
            'units' => $transaction['units'],
            'amount' => $transaction['total_amount'],
            'payment_method' => 'khalti',
            'payment_token' => $response_data['idx'],
            'urgency' => $transaction['urgency']
        ];
        
        // Clear pending transaction
        unset($_SESSION['pending_hospital_transaction']);
        
        $conn->close();
        
        echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed', 'response' => $response_data]);
}
?>