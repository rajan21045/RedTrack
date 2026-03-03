<?php
session_start();

// Clear pending transaction
unset($_SESSION['pending_hospital_transaction']);

// Set cancellation message
$_SESSION['error'] = "Payment cancelled. Your blood request was not processed.";

// Redirect back to hospital dashboard
header("Location: hospital_dashboard.php");
exit();
?>