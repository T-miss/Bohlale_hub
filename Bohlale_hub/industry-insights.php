<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM industries";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Industry Insights</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #E8DFF0;
  --accent1: #ff2b7d;
  --accent2: #b14b7f;
  --deep: #000000;
  --glass: rgba(255, 255, 255, 0.14);
}
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, var(--bg), #fff);
  color: var(--deep);
  margin: 0;
  overflow-x: hidden;
}
header {
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  color: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
header .brand-title { font-weight: 700; font-size: 1.5rem; }
header a { color: white; text-decoration: none; margin-left: 1rem; font-weight: 500; }

main {
  padding: 2.5rem;
  animation: fadeIn 1s ease;
}
.features h2 {
  font-size: 2.3rem;
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
  margin-bottom: 0.5rem;
}
.features p {
  color: #555;
  font-weight: 400;
  margin-bottom: 2rem;
}
.filter {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}
.filter select, .filter input {
  padding: 0.7rem 1rem;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 1rem;
  outline: none;
}
.filter input:focus, .filter select:focus {
  border-color: var(--accent1);
}
.industry-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}
.industry-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
}
.industry-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.2);
}
.industry-card h3 {
  color: var(--accent1);
  font-size: 1.3rem;
  margin-top: 0;
}
.industry-card ul {
  list-style: none;
  padding-left: 0;
}
.industry-card li {
  margin-bottom: 0.4rem;
  color: #333;
}
.download-btn {
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  border: none;
  color: white;
  border-radius: 10px;
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
  cursor: pointer;
  margin-top: 1rem;
  transition: 0.3s ease;
}
.download-btn:hover {
  background: var(--accent2);
  transform: scale(1.05);
}
.trend-chart {
  margin-top: 3rem;
  background: #fff;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.trend-chart h3 {
  color: var(--deep);
  margin-bottom: 1rem;
}
.trend-bar {
  height: 25px;
  border-radius: 12px;
  margin: 0.5rem 0;
  color: white;
  text-align: right;
  line-height: 25px;
  padding-right: 10px;
  background: linear-gradient(90deg, var(--accent1), var(--accent2));
  transition: width 0.8s ease-in-out;
}
footer {
  text-align: center;
  padding: 1.5rem;
  background: #fafafa;
  border-top: 1px solid #ddd;
  margin-top: 3rem;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<header>
  <span class="brand-title">🌐 Student Portal</span>
  <nav>
    <a href="dashboard.php">Home</a>
   
  </nav>
</header>

<main>
<section class="features">
  <h2>Industry Insights Dashboard</h2>
  <p>Explore the latest reports, in-demand skills, and hiring trends across top industries.</p>

  <div class="filter">
    <input type="text" id="searchBox" placeholder="Search industry... 🔍" onkeyup="searchIndustry()">
    <select id="industryFilter" onchange="filterIndustry()">
      <option value="all">All Industries</option>
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
            <li>📅 Monthly Reports: <?php echo $row['reports']; ?></li>
            <li>💡 Top Skills: <?php echo htmlspecialchars($row['top_skills']); ?></li>
            <li>📈 Job Market: <?php echo htmlspecialchars($row['job_market']); ?></li>
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

  <div style="margin-top:2rem; text-align:center;">
    <button class="download-btn" onclick="window.location.href='download_all_reports.php'">
      📚 Download All Reports
    </button>
  </div>

  <div class="trend-chart">
    <h3>📊 Industry Job Trends</h3>
    <div class="trend-bar" style="width: 80%;">IT - 80%</div>
    <div class="trend-bar" style="width: 65%;">Finance - 65%</div>
    <div class="trend-bar" style="width: 72%;">Healthcare - 72%</div>
  </div>
</section>
</main>

<footer>
  <small>© <span id="year"></span> Student Portal | Designed with ❤️ in South Africa</small>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

function filterIndustry() {
  const filter = document.getElementById('industryFilter').value.toLowerCase();
  const cards = document.querySelectorAll('.industry-card');
  cards.forEach(card => {
    card.style.display = (filter === 'all' || card.dataset.type.toLowerCase().includes(filter)) ? 'block' : 'none';
  });
}

function searchIndustry() {
  const search = document.getElementById('searchBox').value.toLowerCase();
  const cards = document.querySelectorAll('.industry-card');
  cards.forEach(card => {
    const title = card.querySelector('h3').textContent.toLowerCase();
    card.style.display = title.includes(search) ? 'block' : 'none';
  });
}
</script>
</body>
</html>
<?php $conn->close(); ?>
