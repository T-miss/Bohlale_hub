<?php
include("db.php");
if(isset($_POST['post_id'], $_POST['username'], $_POST['comment'])){
  $pid = intval($_POST['post_id']);
  $name = $conn->real_escape_string($_POST['username']);
  $msg = $conn->real_escape_string($_POST['comment']);
  $conn->query("INSERT INTO comments (post_id, username, comment) VALUES ($pid, '$name', '$msg')");
  echo "<div class='comment'><b>".htmlspecialchars($name).":</b> ".htmlspecialchars($msg)."</div>";
}
?>
