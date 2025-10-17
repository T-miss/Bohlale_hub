<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Fetch industries
$sql = "SELECT * FROM industries";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Industry Insights</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
  body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; }
  main { padding: 2rem; }

  .features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .features p { margin-bottom: 1rem; color: #555; }

  .industry-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem; }
  .industry-card { background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s; }
  .industry-card:hover { transform: translateY(-5px); }
  .industry-card h3 { margin-top: 0; color: #007bff; }
  .industry-card ul { padding-left: 1rem; }

  .download-btn { margin-top: 0.5rem; padding: 0.5rem 1rem; background: #007bff; color: #fff; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s; }
  .download-btn:hover { background: #0056b3; }

  .filter { margin-bottom: 1rem; }
  .filter select { padding: 0.5rem; border-radius: 8px; border: 1px solid #ccc; }

  .trend-chart { margin-top: 2rem; background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  .trend-chart h3 { margin-top: 0; color: #333; }
  .trend-bar { height: 20px; background: #28a745; border-radius: 10px; margin: 0.3rem 0; transition: width 0.5s; color: #fff; text-align: right; padding-right: 5px; line-height: 20px; font-size: 0.9rem; }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
    <nav class="main-nav">
      <a href="index.html" class="nav-item">Home</a>
      <button class="burger" id="burgerBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </nav>
    <div class="mobile-menu" id="mobileMenu"><a href="index.html">Home</a></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Industry Insights 📊</h2>
  <p>Stay updated with the latest industry trends and job market information for your field.</p>

  <div class="filter">
    <label for="industryFilter">Filter by industry: </label>
    <select id="industryFilter" onchange="filterIndustry()">
      <option value="all">All</option>
      <option value="IT">IT</option>
      <option value="Finance">Finance</option>
      <option value="Healthcare">Healthcare</option>
    </select>
  </div>

  <div class="industry-cards" id="industryCards">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="industry-card" data-type="<?php echo htmlspecialchars($row['name']); ?>">
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <ul>
            <li>Monthly Reports: <?php echo $row['reports']; ?></li>
            <li>Top Skills: <?php echo htmlspecialchars($row['top_skills']); ?></li>
            <li>Job Market: <?php echo htmlspecialchars($row['job_market']); ?></li>
          </ul>
          <button class="download-btn" 
            onclick="window.location.href='download_report.php?industry=<?php echo urlencode($row['name']); ?>'">
            📥 Download <?php echo $row['name']; ?> Report
          </button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No industries found.</p>
    <?php endif; ?>
  </div>

  <div style="margin-top:1.5rem;">
    <button class="download-btn" onclick="window.location.href='download_all_reports.php'">📥 Download All Reports</button>
  </div>

  <div class="trend-chart">
    <h3>Industry Job Trends</h3>
    <div class="trend-bar" style="width: 80%;">IT 80%</div>
    <div class="trend-bar" style="width: 60%;">Finance 60%</div>
    <div class="trend-bar" style="width: 70%;">Healthcare 70%</div>
  </div>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();
const burgerBtn = document.getElementById('burgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
burgerBtn.addEventListener('click', () => {
  const isExpanded = burgerBtn.getAttribute('aria-expanded') === 'true';
  burgerBtn.setAttribute('aria-expanded', !isExpanded);
  mobileMenu.classList.toggle('is-open');
});

function filterIndustry() {
  const filter = document.getElementById('industryFilter').value;
  const cards = document.querySelectorAll('.industry-card');
  cards.forEach(card => {
    if(filter === 'all' || card.dataset.type.includes(filter)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>
</body>
</html>
<?php $conn->close(); ?>


