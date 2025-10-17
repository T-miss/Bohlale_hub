<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$question_id = intval($_POST['question_id']);
$completed = intval($_POST['completed']);

// Example: static user_id = 1
$user_id = 1;

$stmt = $conn->prepare("INSERT INTO tutoring_sessions (user_id, question_id, completed) VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE completed = ?");
$stmt->bind_param("iiii", $user_id, $question_id, $completed, $completed);
$stmt->execute();
$stmt->close();
$conn->close();
?>
