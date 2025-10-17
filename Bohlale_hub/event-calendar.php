<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle tutor upload
$uploadMsg = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_event'])){
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['event_date']);
    $type = $conn->real_escape_string($_POST['type']);
    $desc = $conn->real_escape_string($_POST['description']);
    $fileName = '';

    // Ensure upload folder exists
    $uploadDir = 'pdfs/';
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
    }

    $sql = "INSERT INTO events (title, event_date, type, description, file) VALUES ('$title', '$date', '$type', '$desc', '$fileName')";
    if($conn->query($sql)){
        $uploadMsg = "Event submitted successfully!";
    } else {
        $uploadMsg = "Upload failed: " . $conn->error;
    }
}

// Fetch events
$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);
$events = [];
if($result){
    while($row = $result->fetch_assoc()){
        $events[] = [
            'title' => $row['title'],
            'start' => $row['event_date'],
            'description' => $row['description'],
            'file' => $row['file'],
            'type' => $row['type']
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Calendar</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<style>
main { padding: 2rem; font-family: 'Inter', sans-serif; background: #f9f9f9; }
.features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
.features p { margin-bottom: 1rem; color: #555; }
.event-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem; }
.event-card { background: #fff; border-radius: 15px; padding: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; position: relative; }
.event-card:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
.event-card h3 { margin: 0; font-size: 1.2rem; color: #333; }
.event-card p { margin: 0.5rem 0; color: #666; font-size: 0.9rem; }
.event-card .date { font-weight: 500; color: #ff2b7d; }
.filter-btns { margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap; }
.filter-btns button { padding: 0.4rem 1rem; border: none; border-radius: 10px; background: #ff2b7d; color: #fff; cursor: pointer; transition: background 0.2s; }
.filter-btns button:hover { background: #b14b7f; }
.download-btn { margin-top: 0.5rem; padding: 0.3rem 0.8rem; background: #007bff; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
.download-btn:hover { background: #0056b3; }
#calendar { margin-top: 2rem; background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.fc-event { cursor: pointer; }
.upload-form { background: #fff; padding: 1rem; border-radius: 15px; margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.upload-form input, .upload-form select, .upload-form textarea { width: 100%; margin-bottom: 0.5rem; padding: 0.5rem; border-radius: 8px; border: 1px solid #ccc; }
.upload-form button { background: #28a745; color: #fff; padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; }
.upload-form button:hover { background: #218838; }
.upload-msg { margin-bottom: 1rem; color: green; }
</style>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Event Calendar 📅</h2>
  <p>View upcoming events, workshops, deadlines, and downloadable assignments or memos.</p>

  <!-- Tutor Upload Form -->
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
      <textarea name="description" placeholder="Description" rows="3" required></textarea>
      <input type="file" name="file" accept=".pdf">
      <button type="submit" name="upload_event">Upload Event</button>
    </form>
  </div>

  <!-- Original Filter Buttons -->
  <div class="filter-btns">
    <button onclick="filterEvents('all')">All</button>
    <button onclick="filterEvents('workshop')">Workshops</button>
    <button onclick="filterEvents('deadline')">Deadlines</button>
    <button onclick="filterEvents('seminar')">Seminars</button>
  </div>

  <!-- Original Event Cards -->
  <div class="event-list" id="eventList">
    <?php if(!empty($events)): ?>
        <?php foreach($events as $ev): ?>
        <div class="event-card" data-type="<?= $ev['type'] ?>">
            <h3><?= htmlspecialchars($ev['title']) ?></h3>
            <p class="date"><?= date('M d, Y', strtotime($ev['start'])) ?></p>
            <p><?= htmlspecialchars($ev['description']) ?></p>
            <?php if($ev['file']): ?>
            <button class="download-btn" onclick="downloadPDF('<?= $ev['file'] ?>')">Download</button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
  </div>

  <!-- FullCalendar -->
  <div id='calendar'></div>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Filter cards
function filterEvents(type) {
  const events = document.querySelectorAll('.event-card');
  events.forEach(event => {
    event.style.display = (type === 'all' || event.dataset.type === type) ? 'block' : 'none';
  });
}

// Download PDFs
function downloadPDF(filename) {
  const link = document.createElement('a');
  link.href = 'pdfs/' + filename;
  link.download = filename;
  link.click();
}

// Event card click alert
document.querySelectorAll('.event-card').forEach(card => {
  card.addEventListener('click', e => {
    if(!e.target.classList.contains('download-btn')){
      const title = card.querySelector('h3').textContent;
      const date = card.querySelector('.date').textContent;
      const desc = card.querySelector('p:nth-of-type(2)').textContent;
      alert(title + "\n" + date + "\n" + desc);
    }
  });
});

// FullCalendar
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: <?= json_encode($events) ?>,
    eventDidMount: function(info) {
      if(info.event.extendedProps.file){
        let btn = document.createElement('button');
        btn.className = 'download-btn';
        btn.innerText = 'Download';
        btn.onclick = function(e){
          e.stopPropagation();
          window.location.href = 'pdfs/' + info.event.extendedProps.file;
        };
        info.el.appendChild(btn);
      }
    },
    eventClick: function(info) {
      alert(info.event.title + "\nDate: " + info.event.start.toISOString().split('T')[0] + "\nDescription: " + info.event.extendedProps.description);
    }
  });
  calendar.render();
});
</script>
</body>
</html>
