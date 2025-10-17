<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$type = $_GET['type'] ?? 'all';
if($type === 'all') {
    $sql = "SELECT * FROM events ORDER BY event_date ASC";
} else {
    $stmt = $conn->prepare("SELECT * FROM events WHERE type=? ORDER BY event_date ASC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($events);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $conn->query($sql);
$events = [];
while($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($events);
?>
