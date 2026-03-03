<?php
session_start();

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connections
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
$connHospital = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

// Check connection
if ($connInventory->connect_error || $connHospital->connect_error) {
    die("Connection failed: " . $connInventory->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_name = trim($_POST['h_name']);
    $blood_group = trim($_POST['b_group']);
    $units = (int)$_POST['units'];
    $transport_partner = trim($_POST['transport_partner']);
    
    // DEBUG: Check if transport partner is received
    // Remove this after testing
    error_log("Transport Partner Received: " . $transport_partner);
    
    // Validate input
    if (empty($hospital_name) || empty($blood_group) || $units <= 0 || empty($transport_partner)) {
        $_SESSION['error'] = "Invalid input data! All fields including transport partner are required.";
        header("Location: adminpanel.php");
        exit();
    }
    
    // Check if blood is available in inventory
    $checkStock = $connInventory->prepare("SELECT units FROM blood_inventory WHERE blood_group = ?");
    $checkStock->bind_param("s", $blood_group);
    $checkStock->execute();
    $result = $checkStock->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $available_units = $row['units'];
        
        if ($available_units >= $units) {
            // Calculate price (assuming ₹500 per unit)
            $price_per_unit = 500;
            $total_amount = $units * $price_per_unit;
            
            // Store transaction details in session for payment
            $_SESSION['pending_transaction'] = [
                'hospital_name' => $hospital_name,
                'blood_group' => $blood_group,
                'units' => $units,
                'total_amount' => $total_amount,
                'transport_partner' => $transport_partner
            ];
            
            // DEBUG: Check session data
            // Remove this after testing
            error_log("Session Data: " . print_r($_SESSION['pending_transaction'], true));
            
            // Redirect to payment page
            header("Location: payment_gateway.php");
            exit();
        } else {
            $_SESSION['error'] = "Insufficient stock! Available: $available_units units";
            header("Location: adminpanel.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Blood group not found in inventory!";
        header("Location: adminpanel.php");
        exit();
    }
}

$connInventory->close();
$connHospital->close();
?>