<?php
$host = "localhost";
$user = "root"; // adjust if needed
$pass = "";     // adjust if needed
$db = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT id, question_text, answer_text, hint_text FROM ai_tutoring_questions";
$result = $conn->query($sql);

$questions = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($questions);
$conn->close();
?>
