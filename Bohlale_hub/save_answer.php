<?php
session_start(); // Make sure you have sessions started

$host = "localhost";
$user = "root"; // your DB username
$pass = "";     // your DB password
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$question_id = intval($_POST['question_id']);
$student_answer = $_POST['student_answer'];
$completed = intval($_POST['completed']);

// Use logged-in user ID
$user_id = $_SESSION['user_id'] ?? 1; // Replace 1 with your actual logic

// Insert or update the answer
$stmt = $conn->prepare("
    INSERT INTO student_answers (user_id, question_id, student_answer, completed)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE student_answer=?, completed=?, updated_at=CURRENT_TIMESTAMP
");
$stmt->bind_param("iisiis", $user_id, $question_id, $student_answer, $completed, $student_answer, $completed);

if($stmt->execute()){
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
