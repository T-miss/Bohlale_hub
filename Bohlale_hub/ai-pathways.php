<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM learning_tracks ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Learning Pathways</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
:root {
  --bg: #E8DFF0;
  --accent1: #ff2b7d;
  --accent2: #b14b7f;
  --deep: #2b0028;
  --radius: 20px;
  font-family: 'Inter', sans-serif;
}

html, body {
  height: 100%;
  margin: 0;
  background: linear-gradient(180deg, #E8DFF0, #F7EEF8);
  display: flex;
  flex-direction: column;
  color: var(--deep);
}

/* Header */
header {
  position: sticky;
  top: 0;
  background: rgba(255, 43, 125, 0.15);
  padding: 1rem;
  text-align: center;
  backdrop-filter: blur(10px);
  z-index: 10;
  border-bottom: 2px solid var(--accent1);
}
header .brand-title {
  font-size: 1.8rem;
  color: var(--accent1);
  font-weight: 700;
}

/* Main */
main {
  flex: 1;
  padding: 2rem;
}
.features h2 {
  font-size: 2rem;
  text-align: center;
  color: var(--accent1);
  margin-bottom: 0.5rem;
}
.features p {
  text-align: center;
  color: #555;
  margin-bottom: 2rem;
}
#searchInput {
  width: 100%;
  padding: 0.8rem;
  margin-bottom: 1.5rem;
  border-radius: 12px;
  border: 1px solid #ccc;
  font-size: 1rem;
  transition: 0.3s;
}
#searchInput:focus {
  outline: none;
  border-color: var(--accent1);
  box-shadow: 0 0 8px var(--accent1);
}

/* Tracks Grid */
#trackContainer {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}
.track {
  background: linear-gradient(135deg, #fff, #FFE8F0);
  border-radius: var(--radius);
  padding: 1rem;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: 0.3s;
  overflow: hidden;
  position: relative;
}
.track:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.track h3 {
  margin: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--accent1);
}
.track .icon {
  transition: 0.3s;
  font-size: 1.2rem;
}
.details {
  display: none;
  margin-top: 0.8rem;
  color: #333;
  font-weight: 500;
}
.details ul {
  padding-left: 1.2rem;
}
.details li {
  display: flex;
  align-items: center;
  margin-bottom: 0.4rem;
  transition: 0.3s;
  gap: 0.5rem;
}
.details input[type="checkbox"] {
  margin-right: 0.5rem;
  transform: scale(1.2);
}
.completed {
  text-decoration: line-through;
  color: #28a745;
  font-weight: 600;
}
.badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: var(--accent2);
  color: #fff;
  padding: 0.3rem 0.6rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 700;
  display: none;
}

/* Progress */
.progress-container {
  background: #e0e0e0;
  border-radius: 15px;
  overflow: hidden;
  margin-top: 2rem;
  height: 25px;
}
.progress-bar {
  height: 25px;
  background: #28a745;
  width: 0%;
  text-align: center;
  color: #fff;
  line-height: 25px;
  font-size: 0.9rem;
  transition: width 0.5s;
}

/* Buttons */
.btn-download {
  background: var(--accent1);
  color: #fff;
  padding: 0.8rem 1.4rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  display: block;
  margin: 2rem auto 0 auto;
  transition: 0.3s;
  font-weight: 600;
}
.btn-download:hover {
  background: var(--accent2);
  transform: scale(1.05);
}

/* Footer */
footer {
  background: var(--accent2);
  color: #fff;
  text-align: center;
  padding: 1rem;
  border-top: 2px solid var(--accent1);
  margin-top: auto;
}

/* Animation */
.fade-in {
  animation: fadeIn 0.8s ease forwards;
  opacity: 0;
}
@keyframes fadeIn {
  0% {opacity: 0; transform: translateY(20px);}
  100% {opacity: 1; transform: translateY(0);}
}

/* Confetti */
.confetti {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  pointer-events: none;
  z-index: 9999;
}
.confetti-piece {
  position: absolute;
  width: 8px;
  height: 8px;
  opacity: 0.8;
  animation: fall 3s linear forwards;
}
@keyframes fall {
  0% {transform: translateY(0) rotate(0);}
  100% {transform: translateY(100vh) rotate(720deg);}
}
</style>
</head>
<body>
<header>
  <span class="brand-title">Student Portal</span>
</header>

<main>
<section class="features">
  <h2>AI Learning Pathways 🤖</h2>
  <p>Complete modules to level up and earn badges!</p>

  <input type="text" id="searchInput" placeholder="Search for a track...">

  <div id="trackContainer">
    <?php if($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="track fade-in" data-id="<?php echo $row['id']; ?>">
          <h3>
            <?php echo htmlspecialchars($row['track_name']); ?>
            <span class="icon">▼</span>
          </h3>
          <div class="details">
            <ul>
              <?php 
                $modules = explode(',', $row['modules']);
                foreach($modules as $i => $module) {
                  $id = 'track'.$row['id'].'_module'.$i;
                  echo '<li><input type="checkbox" id="'.$id.'"><label for="'.$id.'">🤖 '.htmlspecialchars(trim($module)).'</label></li>';
                }
              ?>
            </ul>
          </div>
          <div class="badge">🏆 Completed!</div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No learning tracks found.</p>
    <?php endif; ?>
  </div>

  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>

  <button class="btn-download" id="downloadBtn">Download Learning Pathways</button>
</section>
</main>

<footer>
  © <span id="year"></span> Student Portal
</footer>

<div class="confetti" id="confettiContainer"></div>

<script>
document.getElementById("year").textContent = new Date().getFullYear();
const tracks = document.querySelectorAll('.track');
const progressBar = document.getElementById('progressBar');
const confettiContainer = document.getElementById('confettiContainer');

// Toggle track details
tracks.forEach(track => {
  const header = track.querySelector('h3');
  const icon = header.querySelector('.icon');
  const details = track.querySelector('.details');
  const badge = track.querySelector('.badge');
  
  header.addEventListener('click', () => {
    const open = details.style.display === 'block';
    details.style.display = open ? 'none' : 'block';
    icon.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
  });

  const checkboxes = track.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach(cb => {
    const label = cb.nextElementSibling;
    const saved = localStorage.getItem(cb.id);
    if(saved === 'true') {
      cb.checked = true;
      label.classList.add('completed');
    }

    cb.addEventListener('change', () => {
      label.classList.toggle('completed', cb.checked);
      localStorage.setItem(cb.id, cb.checked);
      updateProgress();
      const allDone = Array.from(checkboxes).every(c => c.checked);
      badge.style.display = allDone ? 'block' : 'none';
      if(allDone) triggerConfetti();
    });
  });
});

// Update progress bar
function updateProgress() {
  const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
  const completed = Array.from(allCheckboxes).filter(cb => cb.checked).length;
  const percent = allCheckboxes.length ? Math.round(completed / allCheckboxes.length * 100) : 0;
  progressBar.style.width = percent + '%';
  progressBar.textContent = percent + '%';
}
updateProgress();

// Search filter
document.getElementById('searchInput').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  tracks.forEach(track => {
    const title = track.querySelector('h3').textContent.toLowerCase();
    track.style.display = title.includes(query) ? 'block' : 'none';
  });
});

// Confetti effect
function triggerConfetti() {
  for(let i = 0; i < 80; i++) {
    const confetti = document.createElement('div');
    confetti.classList.add('confetti-piece');
    confetti.style.backgroundColor = ['#ff2b7d', '#b14b7f', '#E8DFF0'][Math.floor(Math.random()*3)];
    confetti.style.left = Math.random() * 100 + 'vw';
    confetti.style.animationDelay = Math.random() * 0.5 + 's';
    confettiContainer.appendChild(confetti);
    setTimeout(() => confetti.remove(), 3000);
  }
}

// Download PDF button
document.getElementById('downloadBtn').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  let y = 10;
  doc.setFontSize(16);
  doc.text("AI Learning Pathways", 10, y);
  y += 10;

  tracks.forEach(track => {
    const title = track.querySelector('h3').textContent;
    doc.setFontSize(14);
    doc.text(title, 10, y);
    y += 8;
    const modules = track.querySelectorAll('li');
    modules.forEach(m => {
      const moduleName = m.textContent.trim();
      const completed = m.querySelector('input').checked ? ' ✅' : '';
      doc.setFontSize(12);
      doc.text("- " + moduleName + completed, 15, y);
      y += 6;
      if(y > 280) { doc.addPage(); y = 10; }
    });
    y += 4;
  });

  doc.save("AI_Learning_Pathways.pdf");
});
</script>
</body>
</html>
<?php $conn->close(); ?>
