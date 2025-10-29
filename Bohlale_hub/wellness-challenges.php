<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);

// Fetch all challenges
$sql = "SELECT * FROM wellness_challenges";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wellness Challenges</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #f4f6f9;
  --accent1: #ff2b7d;
  --accent2: #b14b7f;
  --deep: #2b0028;
  --radius: 15px;
  --container-width: 1100px;
  font-family: 'Inter', sans-serif;
}

html, body {
  margin:0;
  padding:0;
  height:100%;
  display:flex;
  flex-direction:column;
  background: var(--bg);
  color: var(--deep);
}

header {
  position: sticky;
  top: 0;
  background: rgba(255,255,255,0.15);
  padding: 1rem;
  backdrop-filter: blur(10px);
  border-bottom:1px solid rgba(0,0,0,0.1);
  display:flex;
  justify-content: space-between;
  align-items:center;
  z-index:10;
}

header .brand { font-size:1.5rem; font-weight:700; color:var(--accent1); }
header nav a { margin-left:1rem; text-decoration:none; color:var(--deep); font-weight:500; }

main.container {
  flex:1;
  width: min(94%, var(--container-width));
  margin:0 auto;
  padding:2rem 0;
}

.features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--accent1); }
.features p { margin-bottom: 1rem; color: #555; }

.filter-btns { display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap; }
.filter-btns button { padding:0.4rem 1rem; border:none; border-radius:12px; background:var(--accent1); color:#fff; cursor:pointer; transition:0.2s; }
.filter-btns button:hover { background:var(--accent2); }

.challenge-list { display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:1rem; }
.challenge-card { background:#fff; padding:1rem; border-radius: var(--radius); box-shadow:0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s; position: relative; }
.challenge-card:hover { transform: translateY(-5px); }
.challenge-card h3 { margin-top:0; margin-bottom:0.3rem; font-size:1.2rem; color:#333; display:flex; justify-content:space-between; align-items:center; }
.challenge-card p { font-size:0.9rem; color:#666; margin:0.2rem 0; }

.progress-container { background:#eee; border-radius:12px; height:12px; margin:0.5rem 0; overflow:hidden; }
.progress-bar { height:12px; background:var(--accent1); width:0%; transition:width 0.5s; text-align:right; color:#fff; font-size:0.75rem; padding-right:3px; }

.complete-btn { margin-top:0.5rem; padding:0.4rem 0.8rem; border:none; border-radius:8px; background:#28a745; color:#fff; cursor:pointer; transition:0.2s; }
.complete-btn:hover { background:#218838; }

.challenge-card a { display:block; margin-top:5px; font-size:0.8rem; color:var(--accent1); text-decoration:none; }
.challenge-card a:hover { text-decoration:underline; }

footer {
  background: var(--accent2);
  color:#fff;
  text-align:center;
  padding:1rem;
  border-radius:var(--radius);
  margin-top:auto;
}
</style>
</head>
<body>

<header>
  <div class="brand">Student Portal</div>
  <nav>
    <a href="dashboard.php">Home</a>
  </nav>
</header>

<main class="container">
  <section class="features">
    <h2>Wellness Challenges 💪</h2>
    <p>Participate in daily wellness challenges to stay active and healthy. Track your progress and complete tasks!</p>

    <!-- Filter Buttons -->
    <div class="filter-btns">
      <button onclick="filterChallenges('all')">All</button>
      <button onclick="filterChallenges('physical')">Physical</button>
      <button onclick="filterChallenges('mental')">Mental</button>
      <button onclick="filterChallenges('nutrition')">Nutrition</button>
    </div>

    <!-- Challenge List -->
    <div class="challenge-list" id="challengeList">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="challenge-card" data-type="<?= isset($row['type']) ? htmlspecialchars($row['type']) : 'unknown' ?>">
            <h3><?= isset($row['name']) ? htmlspecialchars($row['name']) : 'Unnamed Challenge' ?></h3>
            <p><?= isset($row['description']) ? htmlspecialchars($row['description']) : '' ?></p>
            <div class="progress-container">
              <div class="progress-bar" id="progress<?= $row['id'] ?>">0%</div>
            </div>
            <button class="complete-btn" onclick="completeChallenge('progress<?= $row['id'] ?>')">Mark Complete</button>
            <?php if(isset($row['guide']) && $row['guide'] !== ''): ?>
              <a href="<?= htmlspecialchars($row['guide']) ?>" download>Download Guide 📄</a>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No challenges found.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<footer>
  <div>© <span id="year"></span> Student Portal</div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Complete challenge
function completeChallenge(progressId){
  const bar = document.getElementById(progressId);
  bar.style.width = "100%";
  bar.textContent = "100%";
  alert("Challenge Completed! 🎉");
}

// Filter challenges
function filterChallenges(type){
  const cards = document.querySelectorAll('.challenge-card');
  cards.forEach(card => {
    if(type === 'all' || card.dataset.type === type){
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
