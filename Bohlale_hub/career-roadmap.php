<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Add milestone
if (isset($_POST['addMilestone'])) {
    $title = $conn->real_escape_string($_POST['milestoneTitle']);
    if (!empty($title)) {
        $conn->query("INSERT INTO milestones (title) VALUES ('$title')");
    }
    header("Location: career-roadmap.php");
    exit;
}

// Delete milestone
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM milestones WHERE id=$id");
    header("Location: career-roadmap.php");
    exit;
}

// Fetch milestones
$result = $conn->query("SELECT * FROM milestones ORDER BY created_at ASC");
$milestones = [];
while ($row = $result->fetch_assoc()) {
    $milestones[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Career Roadmaps</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">

<style>
:root {
  --bg: #E8DFF0;
  --accent1: #ff2b7d;
  --accent2: #b14b7f;
  --deep: #000000;
  --glass: rgba(255,255,255,0.12);
}

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, var(--bg), #fff);
  margin: 0;
  color: var(--deep);
  transition: all 0.3s ease;
}

main { padding: 2rem; max-width: 900px; margin: auto; }

.features h2 {
  font-size: 2.2rem;
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
  animation: fadeIn 1.2s ease;
}
.features p { margin-bottom: 1rem; color: #333; }

.roadmap {
  display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;
}

.milestone {
  background: var(--glass);
  backdrop-filter: blur(10px);
  padding: 1rem 1.5rem;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}
.milestone:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.milestone h3 {
  margin: 0;
  color: var(--accent2);
  font-weight: 600;
}

.btn {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 500;
}
.btn-add {
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  color: #fff;
}
.btn-add:hover { opacity: 0.9; }
.btn-download {
  background: #007bff;
  color: #fff;
}
.btn-remove {
  background: #dc3545;
  color: #fff;
}
.btn-remove:hover {
  transform: scale(1.05);
}

.progress-container {
  background: #ddd;
  border-radius: 20px;
  overflow: hidden;
  margin-top: 2rem;
}
.progress-bar {
  height: 22px;
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  width: 0%;
  color: #fff;
  font-size: 0.9rem;
  text-align: center;
  line-height: 22px;
  border-radius: 20px;
  transition: width 0.6s ease;
}

.add-modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  text-align: center;
  width: 90%;
  max-width: 400px;
}
.modal-content input {
  width: 80%;
  padding: 0.6rem;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-top: 1rem;
}
.modal-content .btn { margin-top: 1rem; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<main>
<section class="features">
  <h2>Career Roadmap 🛣️</h2>
  <p>Visualize and celebrate each step of your professional journey!</p>

  <button class="btn btn-add" id="openModal">+ Add Milestone</button>
  <a href="download.php" class="btn btn-download">📥 Download PDF</a>

  <!-- Modal -->
  <div class="add-modal" id="addModal">
    <div class="modal-content">
      <h3>Add a New Milestone</h3>
      <form method="POST">
        <input type="text" name="milestoneTitle" placeholder="Enter milestone..." required>
        <br>
        <button type="submit" name="addMilestone" class="btn btn-add">Save</button>
        <button type="button" class="btn btn-remove" id="closeModal">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Milestones -->
  <div class="roadmap" id="roadmap">
    <?php foreach ($milestones as $m): ?>
    <div class="milestone">
      <div>
        <input type="checkbox" class="milestone-check">
        <h3><?= htmlspecialchars($m['title']) ?></h3>
      </div>
      <a href="career-roadmap.php?delete=<?= $m['id'] ?>" class="btn btn-remove">Remove</a>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>
</section>
</main>

<script>
// Modal controls
const modal = document.getElementById('addModal');
document.getElementById('openModal').onclick = () => modal.style.display = 'flex';
document.getElementById('closeModal').onclick = () => modal.style.display = 'none';
window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; }

// Progress bar updates
function updateProgress() {
  const checks = document.querySelectorAll('.milestone-check');
  const total = checks.length;
  const done = [...checks].filter(c => c.checked).length;
  const percent = total ? (done / total) * 100 : 0;
  const bar = document.getElementById('progressBar');
  bar.style.width = percent + '%';
  bar.textContent = Math.round(percent) + '%';
  if (percent === 100) triggerConfetti();
}

// Confetti animation
function triggerConfetti() {
  const duration = 1200;
  const end = Date.now() + duration;
  const colors = ['#ff2b7d', '#b14b7f', '#007bff'];
  (function frame() {
    const timeLeft = end - Date.now();
    if (timeLeft <= 0) return;
    const particle = document.createElement('div');
    particle.style.position = 'fixed';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = '-10px';
    particle.style.width = '8px';
    particle.style.height = '8px';
    particle.style.background = colors[Math.floor(Math.random()*colors.length)];
    particle.style.borderRadius = '50%';
    particle.style.opacity = 0.8;
    document.body.appendChild(particle);
    const fall = particle.animate([
      { transform: `translateY(0)` },
      { transform: `translateY(${window.innerHeight}px)` }
    ], { duration: 1000 + Math.random() * 1000 });
    fall.onfinish = () => particle.remove();
    requestAnimationFrame(frame);
  })();
}

// Event listener
document.querySelectorAll('.milestone-check').forEach(c => {
  c.addEventListener('change', updateProgress);
});
updateProgress();
</script>

</body>
</html>
