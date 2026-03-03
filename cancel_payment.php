<?php
session_start();

// Clear pending transaction
unset($_SESSION['pending_transaction']);

// Set cancellation message
$_SESSION['error'] = "Payment cancelled. No stock was transferred.";

// Redirect back to admin panel
header("Location: adminpanel.php");
exit();
?>