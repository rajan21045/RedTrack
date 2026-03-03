<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to hospital login page
header("Location: hospital_login.php");
exit();
?>