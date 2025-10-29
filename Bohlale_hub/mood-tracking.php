<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Insert new mood
if(isset($_POST['saveMood'])) {
  $mood = $conn->real_escape_string($_POST['mood']);
  $note = $conn->real_escape_string($_POST['note']);
  $entry_date = date("Y-m-d H:i:s");
  $conn->query("INSERT INTO mood_tracker (mood, note, entry_date) VALUES ('$mood', '$note', '$entry_date')");
  echo "<script>localStorage.setItem('moodSubmitted', 'true'); window.location.reload();</script>";
  exit;
}

// Fetch saved moods
$res2 = $conn->query("SELECT * FROM mood_tracker ORDER BY entry_date DESC");

// Prepare data for chart (last 7 days)
$chartData = [];
for($i=6;$i>=0;$i--){
    $day = date('Y-m-d', strtotime("-$i days"));
    $chartData[$day] = ['Happy 😊'=>0,'Calm 😌'=>0,'Focused 🧠'=>0,'Anxious 😟'=>0,'Tired 😴'=>0];
}
$sql = "SELECT mood, DATE(entry_date) as day, COUNT(*) as cnt FROM mood_tracker 
        WHERE DATE(entry_date) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
        GROUP BY day, mood";
$res = $conn->query($sql);
if($res){
    while($row=$res->fetch_assoc()){
        if(isset($chartData[$row['day']][$row['mood']])) $chartData[$row['day']][$row['mood']] = (int)$row['cnt'];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mood Tracker 💖</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --deep:#2b0028;
  --glass: rgba(255,255,255,0.14);
}

body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(180deg,var(--bg),#f7eef8 60%);
  color: var(--deep);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

header {
  position: fixed;
  top: 0;
  width: 100%;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(12px);
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 100;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

header h1 {
  font-weight: 600;
  font-size: 1.5rem;
  color: var(--accent1);
}

header a {
  color: var(--deep);
  text-decoration: none;
  background: var(--accent2);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: 0.3s;
}
header a:hover {
  background: var(--accent1);
  color: white;
}

main {
  flex: 1;
  margin-top: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem;
}

.container {
  background: var(--glass);
  border-radius: 20px;
  backdrop-filter: blur(15px);
  padding: 2rem;
  max-width: 600px;
  width: 100%;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  text-align: center;
}

h2 {
  color: var(--accent1);
  margin-bottom: 1rem;
}

textarea, select {
  width: 100%;
  border-radius: 12px;
  border: 1px solid #ccc;
  padding: 0.7rem;
  font-family: 'Poppins';
  margin-bottom: 1rem;
  resize: none;
}

button {
  background: var(--accent1);
  color: white;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}
button:hover {
  background: var(--accent2);
  color: white;
}

.mood-history {
  margin-top: 2rem;
  width: 100%;
}
.mood-entry {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 0.7rem;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
}

footer {
  text-align: center;
  padding: 1rem;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  color: var(--deep);
  font-size: 0.9rem;
  margin-top: auto;
}

canvas {
  margin-top: 2rem;
  background: white;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.07);
}

.emoji {
  position: fixed;
  font-size: 2rem;
  opacity: 0;
  animation: floatUp 2s ease forwards;
  pointer-events: none;
}

@keyframes floatUp {
  0% { transform: translateY(0); opacity: 1; }
  100% { transform: translateY(-150px); opacity: 0; }
}
</style>
</head>
<body>

<header>
  <h1>Mood Tracker 💖</h1>
  <a href="dashboard.php">Home</a>
</header>

<main>
  <div class="container">
    <h2>Track Your Mood</h2>
    <form method="POST">
      <select name="mood" id="mood" required>
        <option value="">Select Mood</option>
        <option value="Happy 😊">Happy 😊</option>
        <option value="Calm 😌">Calm 😌</option>
        <option value="Focused 🧠">Focused 🧠</option>
        <option value="Anxious 😟">Anxious 😟</option>
        <option value="Tired 😴">Tired 😴</option>
      </select>
      <textarea name="note" placeholder="Write a short note..."></textarea>
      <button type="submit" name="saveMood">Save Mood</button>
    </form>

    <div class="mood-history">
      <h3>Recent Entries</h3>
      <?php if($res2 && $res2->num_rows>0):
          while($row = $res2->fetch_assoc()): ?>
          <div class="mood-entry">
            <strong><?php echo htmlspecialchars($row['mood']); ?></strong><br>
            <small><?php echo htmlspecialchars($row['entry_date']); ?></small><br>
            <p><?php echo htmlspecialchars($row['note']); ?></p>
          </div>
      <?php endwhile; else: ?>
        <p>No moods tracked yet.</p>
      <?php endif; ?>
    </div>

    <canvas id="moodChart" width="400" height="200"></canvas>

    <button id="downloadBtn">📘 Download Mood Guide</button>
  </div>
</main>

<footer>
  © <span id="year"></span> Bohlale Hub — Reflect. Grow. Shine. 🌙
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Emoji animation when mood submitted
if(localStorage.getItem('moodSubmitted')) {
  localStorage.removeItem('moodSubmitted');
  const emojis = ['😊','😌','🧠','😟','😴'];
  for(let i=0;i<10;i++){
    const emoji = document.createElement('div');
    emoji.classList.add('emoji');
    emoji.innerText = emojis[Math.floor(Math.random()*emojis.length)];
    emoji.style.left = Math.random()*window.innerWidth + 'px';
    emoji.style.bottom = '20px';
    emoji.style.animationDelay = (i*0.2)+'s';
    document.body.appendChild(emoji);
    setTimeout(()=>emoji.remove(),2000);
  }
}

// Generate PDF
document.getElementById('downloadBtn').addEventListener('click', function() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  let y = 10;
  doc.setFontSize(18);
  doc.text("My Mood Journal 💖", 10, y);
  y += 10;
  document.querySelectorAll('.mood-entry').forEach(entry => {
    const text = entry.innerText.trim();
    const lines = doc.splitTextToSize(text, 180);
    doc.text(lines, 10, y);
    y += lines.length * 7 + 5;
    if(y > 270) { doc.addPage(); y = 10; }
  });
  doc.save("My_Mood_Guide.pdf");
});

// Dynamic Chart Data
const chartData = <?php echo json_encode($chartData); ?>;
const labels = Object.keys(chartData);
const moods = ['Happy 😊','Calm 😌','Focused 🧠','Anxious 😟','Tired 😴'];
const datasets = moods.map((mood, i) => ({
    label: mood,
    data: labels.map(day => chartData[day][mood]),
    backgroundColor: `rgba(255,43,125,0.3)`,
    borderColor: `rgba(177,75,127,1)`,
    fill: true,
    tension: 0.3
}));

new Chart(document.getElementById('moodChart'), {
  type: 'line',
  data: { labels, datasets },
  options: {
    responsive:true,
    plugins: {
      legend: { position: 'top' }
    },
    scales: {
      y: { beginAtZero: true, stepSize: 1 }
    }
  }
});
</script>

</body>
</html>

