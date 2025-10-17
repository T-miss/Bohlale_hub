<?php
include("db.php");
if(isset($_POST['username'], $_POST['message'])){
  $name = $conn->real_escape_string($_POST['username']);
  $msg = $conn->real_escape_string($_POST['message']);
  $conn->query("INSERT INTO posts (username, message) VALUES ('$name', '$msg')");
  $id = $conn->insert_id;
  echo "<div class='post' data-id='$id'>
          <h4>".htmlspecialchars($name)."</h4>
          <p>".nl2br(htmlspecialchars($msg))."</p>
          <button class='like-btn' onclick='likePost($id,this)'>Like (0)</button>
        </div>";
}
?>
