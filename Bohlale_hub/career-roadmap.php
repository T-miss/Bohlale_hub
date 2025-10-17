<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Handle Add Milestone
if (isset($_POST['addMilestone'])) {
    $title = $conn->real_escape_string($_POST['milestoneTitle']);
    if (!empty($title)) {
        $conn->query("INSERT INTO milestones (title) VALUES ('$title')");
    }
    header("Location: career-roadmap.php");
    exit;
}

// Handle Remove Milestone
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
<link rel="stylesheet" href="styles.css">
<style>
  body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; }
  main { padding: 2rem; }

  .features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .features p { margin-bottom: 1rem; color: #555; }

  .roadmap { display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; }
  .milestone { background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; transition: transform 0.2s; }
  .milestone:hover { transform: translateY(-3px); }
  .milestone h3 { margin: 0; color: #007bff; }

  .btn { padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s; }
  .btn-add { background: #28a745; color: #fff; }
  .btn-add:hover { background: #218838; }
  .btn-download { background: #007bff; color: #fff; }
  .btn-download:hover { background: #0056b3; }
  .btn-remove { background: #dc3545; color: #fff; margin-left: 0.5rem; }
  .btn-remove:hover { background: #c82333; }

  .progress-container { background: #e0e0e0; border-radius: 15px; overflow: hidden; margin-top: 1rem; }
  .progress-bar { height: 20px; background: #007bff; width: 0%; text-align: center; color: #fff; line-height: 20px; font-size: 0.9rem; transition: width 0.5s; }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
    <nav class="main-nav">
      <a href="index.php" class="nav-item">Home</a>
      <button class="burger" id="burgerBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </nav>
    <div class="mobile-menu" id="mobileMenu"><a href="index.php">Home</a></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Career Roadmaps 🛣️</h2>
  <p>Plan your career with step-by-step guidance and visualize milestones for your dream job.</p>

  <!-- Add Milestone -->
  <form method="POST" style="margin-bottom:1rem;">
    <input type="text" name="milestoneTitle" placeholder="Enter milestone..." required>
    <button type="submit" name="addMilestone" class="btn btn-add">Add Milestone</button>
    <a href="download.php" class="btn btn-download">Download Roadmap PDF</a>
  </form>

  <!-- Milestones -->
  <div class="roadmap" id="roadmap">
    <?php foreach ($milestones as $m): ?>
    <div class="milestone">
      <h3><?= htmlspecialchars($m['title']) ?></h3>
      <a href="career-roadmap.php?delete=<?= $m['id'] ?>" class="btn btn-remove">Remove</a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Progress -->
  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Update Progress
function updateProgress() {
  const milestones = document.querySelectorAll('.roadmap .milestone');
  const completed = milestones.length;
  const progressPercent = milestones.length ? Math.min(100, completed / 5 * 100) : 0;
  const bar = document.getElementById('progressBar');
  bar.style.width = progressPercent + '%';
  bar.textContent = Math.round(progressPercent) + '%';
}
updateProgress();
</script>
</body>
</html>
