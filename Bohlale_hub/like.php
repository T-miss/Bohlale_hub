<?php
include("db.php");
if(isset($_POST['id'])){
  $id = intval($_POST['id']);
  $conn->query("UPDATE posts SET likes = likes + 1 WHERE id=$id");
  $res = $conn->query("SELECT likes FROM posts WHERE id=$id");
  $row = $res->fetch_assoc();
  echo $row['likes'];
}
?>
