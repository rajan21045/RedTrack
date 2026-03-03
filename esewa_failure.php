<?php
session_start();

$_SESSION['error'] = "eSewa payment failed or was cancelled. Please try again.";
header("Location: hospital_dashboard.php");
exit();
?>