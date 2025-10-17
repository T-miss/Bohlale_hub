<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = 1; // Replace with session user_id

$sql = "SELECT question_id, student_answer, completed FROM student_answers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$answers = [];
while($row = $result->fetch_assoc()) {
    $answers[$row['question_id']] = $row;
}

header('Content-Type: application/json');
echo json_encode($answers);
$stmt->close();
$conn->close();
?>
