<?php
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(isset($_POST['profile_id'], $_POST['user_name'])) {
    $profile_id = intval($_POST['profile_id']);
    $user_name = $conn->real_escape_string($_POST['user_name']);
    $stmt = $conn->prepare("INSERT INTO connections (profile_id, user_name) VALUES (?, ?)");
    $stmt->bind_param("is", $profile_id, $user_name);
    $stmt->execute();
    $stmt->close();
}
?>
