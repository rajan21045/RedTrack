<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];

// Database connection
$conn = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create blood_requests table if not exists
$createRequestTable = "CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT NOT NULL,
    hospital_name VARCHAR(255) NOT NULL,
    blood_group VARCHAR(10) NOT NULL,
    units INT NOT NULL,
    urgency VARCHAR(20) NOT NULL,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
)";
$conn->query($createRequestTable);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blood_group = $_POST['blood_group'];
    $units = (int)$_POST['units'];
    $urgency = $_POST['urgency'];
    $notes = trim($_POST['notes']);
    
    // Validate input
    if (empty($blood_group) || $units <= 0 || empty($urgency)) {
        $_SESSION['error'] = "Invalid input data!";
        header("Location: hospital_dashboard.php");
        exit();
    }
    
    // Check if blood is available in inventory
    $checkStock = $conn->prepare("SELECT units FROM blood_inventory WHERE blood_group = ?");
    $checkStock->bind_param("s", $blood_group);
    $checkStock->execute();
    $result = $checkStock->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $available_units = $row['units'];
        
        if ($available_units >= $units) {
            // Calculate price (₹500 per unit)
            $price_per_unit = 500;
            $total_amount = $units * $price_per_unit;
            
            // Store request in database
            $insertRequest = $conn->prepare("INSERT INTO blood_requests (hospital_id, hospital_name, blood_group, units, urgency, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $insertRequest->bind_param("ississ", $hospital_id, $hospital_name, $blood_group, $units, $urgency, $notes);
            $insertRequest->execute();
            
            // Store transaction details in session for payment
            $_SESSION['pending_hospital_transaction'] = [
                'hospital_id' => $hospital_id,
                'hospital_name' => $hospital_name,
                'blood_group' => $blood_group,
                'units' => $units,
                'urgency' => $urgency,
                'notes' => $notes,
                'total_amount' => $total_amount
            ];
            
            // Redirect to payment page
            header("Location: hospital_payment.php");
            exit();
        } else {
            $_SESSION['error'] = "Insufficient stock! Only $available_units units available for $blood_group";
            header("Location: hospital_dashboard.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Blood group not found in inventory!";
        header("Location: hospital_dashboard.php");
        exit();
    }
}

$conn->close();
?>