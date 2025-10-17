<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['event_date'];
    $desc = $_POST['description'];
    $type = $_POST['type'];
    $filename = null;

    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['file']['tmp_name'], "pdfs/$filename");
    }

    $stmt = $conn->prepare("INSERT INTO events (title, event_date, description, type, file) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $title, $date, $desc, $type, $filename);
    $stmt->execute();
    $stmt->close();
    echo "<p>Event uploaded successfully! <a href='event_calendar.php'>Go to Calendar</a></p>";
}

$conn->close();
?>

<form method="POST" enctype="multipart/form-data">
  <h2>Upload Event</h2>
  <label>Title: <input type="text" name="title" required></label><br>
  <label>Date: <input type="date" name="event_date" required></label><br>
  <label>Description:<br>
    <textarea name="description" rows="4" cols="50" required></textarea>
  </label><br>
  <label>Type:
    <select name="type">
      <option value="workshop">Workshop</option>
      <option value="deadline">Deadline</option>
      <option value="seminar">Seminar</option>
    </select>
  </label><br>
  <label>Upload PDF (optional): <input type="file" name="file" accept=".pdf"></label><br>
  <button type="submit">Upload Event</button>
</form>
