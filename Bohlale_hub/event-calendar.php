<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle delete
if (isset($_POST['delete_event'])) {
    $eventId = intval($_POST['delete_event']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Upload event
$uploadMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_event'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['event_date']);
    $type = $conn->real_escape_string($_POST['type']);
    $desc = $conn->real_escape_string($_POST['description']);

    $uploadDir = 'pdfs/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = '';
    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['file']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($fileTmp, $targetPath);
    }

    $sql = "INSERT INTO events (title, event_date, type, description, file)
            VALUES ('$title', '$date', '$type', '$desc', " . 
            ($fileName ? "'$fileName'" : "NULL") . ")";
    $uploadMsg = $conn->query($sql) ? "✅ Event added successfully!" : "❌ Upload failed: " . $conn->error;
}

// Fetch events
$events = [];
$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bohlale Hub – Smart Event Manager</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet'>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<style>
:root{
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --deep:#2b0028;
  --radius:18px;
  font-family:'Inter',system-ui;
}
body{
  margin:0;
  background:linear-gradient(180deg,var(--bg),#f7eef8 70%);
  color:var(--deep);
  font-family:'Inter',sans-serif;
  overflow-x:hidden;
}
header{
  position:sticky;top:0;z-index:100;
  backdrop-filter:blur(14px);
  background:rgba(255,255,255,0.7);
  box-shadow:0 2px 12px rgba(0,0,0,0.05);
  padding:1rem 2rem;
  display:flex;justify-content:space-between;align-items:center;
}
.brand{
  display:flex;align-items:center;gap:12px;
}
.brand .logo{
  width:42px;height:42px;
  background:linear-gradient(135deg,var(--accent1),var(--accent2));
  border-radius:12px;
  box-shadow:0 5px 15px rgba(177,75,127,0.3);
}
.brand h1{font-size:1.2rem;margin:0;font-weight:700;color:var(--deep);}
#clock{font-weight:600;color:var(--accent2);}

main{padding:2rem;max-width:1100px;margin:auto;text-align:center;}
.upload-form{
  background:rgba(255,255,255,0.4);
  border-radius:var(--radius);
  padding:1.5rem 2rem;
  box-shadow:0 8px 20px rgba(0,0,0,0.05);
  margin-bottom:1.5rem;
  width:70%;
  margin-left:auto;
  margin-right:auto;
}
.upload-form input,
.upload-form select,
.upload-form textarea{
  width:90%;
  padding:12px;
  margin-bottom:1rem;
  border-radius:10px;
  border:1px solid #bbb;
  font-family:inherit;
  font-size:0.95rem;
  transition:all .2s ease;
}
.upload-form input:focus,
.upload-form select:focus,
.upload-form textarea:focus{
  outline:none;
  border-color:var(--accent2);
  box-shadow:0 0 4px rgba(177,75,127,0.4);
}
.upload-form button{
  background:linear-gradient(135deg,var(--accent1),var(--accent2));
  border:none;color:#fff;padding:10px 20px;
  border-radius:12px;cursor:pointer;
  font-weight:700;transition:opacity .2s;
}
.upload-form button:hover{opacity:.85;}
.upload-msg{color:green;font-weight:600;margin-bottom:1rem;}

.event-list{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:1rem;
}
.event-card{
  background:#fff;border-radius:var(--radius);
  padding:1rem;box-shadow:0 8px 20px rgba(0,0,0,0.08);
  position:relative;
}
.event-card::before{
  content:'';position:absolute;top:0;left:0;width:100%;height:6px;
  background:linear-gradient(90deg,var(--accent1),var(--accent2));
}
.event-card h3{margin:0;color:var(--deep);}
.event-card p{margin:6px 0;color:#555;}
.event-card .date{color:var(--accent2);font-weight:600;}
.btn-box{display:flex;justify-content:space-between;margin-top:10px;}
button.download-btn, button.delete-btn{
  border:none;padding:6px 12px;border-radius:8px;font-size:0.85rem;
  cursor:pointer;font-weight:600;
}
.download-btn{
  background:linear-gradient(135deg,var(--accent1),var(--accent2));
  color:white;
}
.download-btn:hover{opacity:.9;}
.delete-btn{
  background:#eee;color:#a00;border:1px solid #ccc;
}
.delete-btn:hover{background:#f8d7da;}
#calendar{
  margin-top:2rem;background:#fff;border-radius:var(--radius);
  box-shadow:0 6px 16px rgba(0,0,0,0.08);padding:1rem;
}
.today-highlight{
  background:linear-gradient(135deg,var(--accent1),var(--accent2));
  color:white;padding:0.8rem 1rem;border-radius:12px;
  box-shadow:0 6px 14px rgba(0,0,0,0.15);
  margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;
}
footer{
  text-align:center;padding:1rem;margin-top:2rem;
  color:#4b2e43;opacity:0.9;
}
</style>
</head>
<body>

<header>
  <div class="brand">
    <div class="logo"></div>
    <h1>Bohlale Hub Events</h1>
  </div>
  <div id="clock"></div>
</header>

<main>
  
  <div class="today-highlight">
    
    <span>🌞 <strong>Today:</strong> <span id="today-date"></span></span>
    <span>Stay inspired, keep learning!</span>
  </div>

  <h2>📅 Event Calendar</h2>
  <p>Upload, manage, and track your events in real time.</p>

  <div class="upload-form">
    <?php if($uploadMsg) echo "<div class='upload-msg'>$uploadMsg</div>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Event Title" required>
      <input type="date" name="event_date" required>
      <select name="type" required>
        <option value="">Select Event Type</option>
        <option value="workshop">Workshop</option>
        <option value="deadline">Deadline</option>
        <option value="seminar">Seminar</option>
      </select>
      <textarea name="description" rows="3" placeholder="Short description..." required></textarea>
      <input type="file" name="file" accept=".pdf">
      <button type="submit" name="upload_event">Upload Event</button>
    </form>
  </div>

  <div class="event-list" id="eventList">
    <?php if(!empty($events)): ?>
        <?php foreach($events as $ev): ?>
        <div class="event-card" data-type="<?= htmlspecialchars($ev['type']) ?>">
          <h3><?= htmlspecialchars($ev['title']) ?></h3>
          <p class="date"><?= date('M d, Y', strtotime($ev['event_date'])) ?></p>
          <p><?= htmlspecialchars($ev['description']) ?></p>
          <div class="btn-box">
            <?php if(!empty($ev['file']) && file_exists('pdfs/' . $ev['file'])): ?>
              <button class="download-btn" onclick="downloadPDF('<?= htmlspecialchars($ev['file']) ?>')">Download</button>
            <?php else: ?>
              <button disabled class="download-btn" style="opacity:0.5;">No File</button>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('Delete this event?');" style="display:inline;">
              <button class="delete-btn" type="submit" name="delete_event" value="<?= $ev['id'] ?>">Delete</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
  </div>

  <div id="calendar"></div>
</main>

<footer>© <span id="year"></span> Bohlale Hub — Smart Student Platform</footer>

<script>
// Live clock
setInterval(()=>{document.getElementById("clock").textContent = new Date().toLocaleTimeString();},1000);
document.getElementById("today-date").textContent = new Date().toDateString();
document.getElementById("year").textContent = new Date().getFullYear();

// Download
function downloadPDF(file){
  const link=document.createElement('a');
  link.href='pdfs/'+file;
  link.download=file;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Calendar
document.addEventListener('DOMContentLoaded',()=>{
  const calendarEl=document.getElementById('calendar');
  const events=<?= json_encode($events) ?>;
  const calendar=new FullCalendar.Calendar(calendarEl,{
    initialView:'dayGridMonth',
    headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},
    events: events.map(e=>({
      title:e.title,
      start:e.event_date,
      description:e.description
    })),
    eventClick:(info)=>{
      alert(info.event.title + "\nDate: " + info.event.startStr + "\n" + info.event.extendedProps.description);
    }
  });
  calendar.render();
});
</script>
</body>
</html>

