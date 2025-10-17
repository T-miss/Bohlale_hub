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
body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; }
main { padding: 2rem; }
.features h2 { font-size: 2rem; margin-bottom: 0.5rem; text-align: center; }
.features p { margin-bottom: 2rem; color: #555; text-align: center; }

#searchInput { width: 100%; padding: 0.5rem; margin-bottom: 1.5rem; border-radius: 8px; border: 1px solid #ccc; }

.track { background: #fff; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; transition: 0.3s; position: relative; overflow: hidden; }
.track:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 6px 14px rgba(0,0,0,0.15); }
.track h3 { margin: 0 0 0.5rem 0; color: #007bff; display: flex; justify-content: space-between; align-items: center; }
.track .icon { transition: transform 0.3s; }
.details { display: none; margin-top: 0.5rem; color: #333; font-weight: 500; }
.details ul { padding-left: 1.2rem; }
.details li { display: flex; align-items: center; margin-bottom: 0.3rem; transition: 0.3s; }
.details input[type="checkbox"] { margin-right: 0.5rem; }

.completed { text-decoration: line-through; color: #28a745; font-weight: 600; }

.btn-download { background: #6f42c1; color: #fff; padding: 0.5rem 1rem; border: none; border-radius: 8px; margin-top: 1rem; cursor: pointer; display: block; margin-left: auto; margin-right: auto; }
.btn-download:hover { background: #5936a2; }

.progress-container { background: #e0e0e0; border-radius: 15px; overflow: hidden; margin-top: 2rem; height: 25px; }
.progress-bar { height: 25px; background: #28a745; width: 0%; text-align: center; color: #fff; line-height: 25px; font-size: 0.9rem; transition: width 0.5s; }

.fade-in { animation: fadeIn 0.8s ease forwards; opacity: 0; }
@keyframes fadeIn { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><span class="brand-title">Student Portal</span></div>
  </div>
</header>

<main>
<section class="features">
  <h2>AI Learning Pathways 🤖</h2>
  <p>Track your learning progress and download your completed modules!</p>

  <input type="text" id="searchInput" placeholder="Search for a track...">

  <div id="trackContainer">
    <?php if($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="track fade-in">
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
                    echo '<li><input type="checkbox" id="'.$id.'"><label for="'.$id.'">'.htmlspecialchars(trim($module)).'</label></li>';
                }
              ?>
            </ul>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No learning tracks found.</p>
    <?php endif; ?>
  </div>

  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>

  <button class="btn-download" onclick="downloadPDF()">Download Learning Pathways</button>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

const tracks = document.querySelectorAll('.track');
const progressBar = document.getElementById('progressBar');

// Toggle track details
tracks.forEach((track, trackIndex) => {
  const header = track.querySelector('h3');
  const icon = header.querySelector('.icon');
  header.addEventListener('click', () => {
    const details = track.querySelector('.details');
    if(details.style.display === 'block') {
      details.style.display = 'none';
      icon.style.transform = 'rotate(0deg)';
    } else {
      details.style.display = 'block';
      icon.style.transform = 'rotate(180deg)';
    }
  });

  // Module checkboxes
  const checkboxes = track.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach((cb, i) => {
    const label = cb.nextElementSibling;
    // Load state from localStorage
    const saved = localStorage.getItem(cb.id);
    if(saved === 'true') {
      cb.checked = true;
      label.classList.add('completed');
    }

    cb.addEventListener('change', () => {
      label.classList.toggle('completed', cb.checked);
      localStorage.setItem(cb.id, cb.checked);
      updateProgress();
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

// Download PDF
function downloadPDF() {
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
}
</script>
</body>
</html>
<?php $conn->close(); ?>
