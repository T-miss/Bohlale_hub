<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }

$sql = "SELECT * FROM quiz_questions";
$result = $conn->query($sql);

$questions = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $questions[] = [
            'q' => $row['question'],
            'options' => [$row['option1'], $row['option2'], $row['option3'], $row['option4']],
            'a' => $row['correct_option'] - 1
        ];
    }
}

echo json_encode($questions);
$conn->close();
?>
