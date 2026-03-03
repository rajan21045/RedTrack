<?php
$conn = new mysqli(
    "localhost",
    "root",
    "*rajan12345#",
    "blood_donation"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
