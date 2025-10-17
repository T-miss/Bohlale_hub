<?php
$host = "localhost";
$user = "root"; // change if needed
$pass = "";     // change if needed
$dbname = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
