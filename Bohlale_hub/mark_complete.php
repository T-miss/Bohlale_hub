<?php
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) die(json_encode(["success"=>false]));

$id = intval($_POST['id']);
$conn->query("UPDATE wellness_challenges SET status='completed' WHERE id=$id");

// Return updated stats
$total = $conn->query("SELECT * FROM wellness_challenges")->num_rows;
$completed = $conn->query("SELECT * FROM wellness_challenges WHERE status='completed'")->num_rows;

echo json_encode(["success"=>true, "completed"=>$completed, "total"=>$total]);
?>
