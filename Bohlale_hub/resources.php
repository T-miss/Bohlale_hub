<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
  die("DB Connection failed: " . $conn->connect_error);
}

// Fetch resources
$sql = "SELECT * FROM resources";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Study Resources</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #E8DFF0;
    --accent1: #ff2b7d;
    --accent2: #b14b7f;
    --deep: #2b0028;
    --glass: rgba(255, 255, 255, 0.14);
  }

  body {
    font-family: 'Inter', sans-serif;
    background: var(--bg);
    color: var(--deep);
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: var(--accent2);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  }

  .brand-title { font-weight: 700; font-size: 1.4rem; }

  main {
    flex: 1;
    padding: 2rem;
    max-width: 1100px;
    margin: auto;
    text-align: center;
  }

  h1 {
    color: var(--accent2);
    font-size: 2rem;
    margin-bottom: 1rem;
  }

  p {
    color: #555;
    font-size: 1rem;
    margin-bottom: 2rem;
  }

  .filter {
    margin-bottom: 1.5rem;
  }

  .filter select {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 1rem;
    background: white;
  }

  .resource-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
  }

  .resource-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    padding: 1.5rem;
    transition: all 0.3s ease;
    text-align: left;
  }

  .resource-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
  }

  .resource-card h3 {
    color: var(--accent1);
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
  }

  .resource-card p {
    color: #444;
    margin-bottom: 0.8rem;
  }

  .download-btn {
    background: var(--accent1);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s ease;
  }

  .download-btn:hover {
    background: var(--accent2);
  }

  footer {
    background: var(--deep);
    color: white;
    padding: 1rem;
    text-align: center;
    margin-top: auto;
  }

  footer small {
    color: #f3f3f3;
  }
</style>
</head>
<body>

<header>
  <span class="brand-title">Bohlale hub</span>
  <nav>
    <a href="dashboard.php" style="color:white;text-decoration:none;margin-right:15px;">Home</a>
   
  </nav>
</header>

<main>
  <h1>📘 Study Resources</h1>
  <p>Explore downloadable study guides, tools, and materials to help you stay ahead.</p>

  <div class="filter">
    <label for="resourceFilter">Filter by Type:</label>
    <select id="resourceFilter" onchange="filterResources()">
      <option value="all">All</option>
      <option value="study">Study Guides</option>
      <option value="tool">Tools</option>
      <option value="support">Support Materials</option>
    </select>
  </div>

  <div class="resource-grid" id="resourceGrid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="resource-card" data-type="<?php echo htmlspecialchars($row['type']); ?>">
          <h3><?php echo htmlspecialchars($row['title']); ?></h3>
          <p><?php echo htmlspecialchars($row['description']); ?></p>
          <button class="download-btn" onclick="window.location.href='<?php echo $row['file_path']; ?>'">
            📥 Download PDF
          </button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No resources found. Add some study guides to your database!</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <small>© <span id="year"></span> Student Portal | Learn. Grow. Succeed.</small>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

function filterResources() {
  const filter = document.getElementById('resourceFilter').value;
  const cards = document.querySelectorAll('.resource-card');
  cards.forEach(card => {
    if (filter === 'all' || card.dataset.type === filter) {
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
