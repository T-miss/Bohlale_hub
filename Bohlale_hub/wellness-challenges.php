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
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; }
main { padding: 2rem; }
.features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
.features p { margin-bottom: 1rem; color: #555; }
.filter-btns { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
.filter-btns button { padding: 0.4rem 1rem; border: none; border-radius: 12px; background: #ff2b7d; color: #fff; cursor: pointer; transition: 0.2s; }
.filter-btns button:hover { background: #b14b7f; }
.challenge-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
.challenge-card { background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s; }
.challenge-card:hover { transform: translateY(-5px); }
.challenge-card h3 { margin-top: 0; margin-bottom: 0.3rem; font-size: 1.2rem; color: #333; display: flex; align-items: center; gap: 0.5rem; }
.challenge-card p { font-size: 0.9rem; color: #666; margin: 0.2rem 0; }
.progress-container { background: #eee; border-radius: 12px; height: 10px; margin: 0.5rem 0; overflow: hidden; }
.progress-bar { height: 10px; background: #007bff; width: 0%; transition: width 0.5s; }
.complete-btn { margin-top: 0.5rem; padding: 0.4rem 0.8rem; border: none; border-radius: 8px; background: #28a745; color: #fff; cursor: pointer; }
.complete-btn:hover { background: #218838; }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
    <nav class="main-nav">
      <a href="dashboard.html" class="nav-item">Home</a>
      <button class="burger" id="burgerBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </nav>
    <div class="mobile-menu" id="mobileMenu"><a href="dashboard.html">Home</a></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Wellness Challenges 💪</h2>
  <p>Participate in daily wellness challenges to stay active and healthy. Track your progress and complete tasks!</p>

  <div class="filter-btns">
    <button onclick="filterChallenges('all')">All</button>
    <button onclick="filterChallenges('physical')">Physical</button>
    <button onclick="filterChallenges('mental')">Mental</button>
    <button onclick="filterChallenges('nutrition')">Nutrition</button>
  </div>

  <div class="challenge-list" id="challengeList">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <div class="challenge-card" data-type="<?php echo $row['type']; ?>">
        <h3><?php echo $row['name']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <div class="progress-container"><div class="progress-bar" id="progress<?php echo $row['id']; ?>"></div></div>
        <button class="complete-btn" onclick="completeChallenge('progress<?php echo $row['id']; ?>')">Mark Complete</button>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No challenges found.</p>
    <?php endif; ?>
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

// Complete challenge
function completeChallenge(progressId) {
  const progress = document.getElementById(progressId);
  progress.style.width = "100%";
  alert("Challenge Completed!");
}

// Filter challenges by type
function filterChallenges(type) {
  const challenges = document.querySelectorAll('.challenge-card');
  challenges.forEach(challenge => {
    if(type === 'all' || challenge.dataset.type === type) {
      challenge.style.display = 'block';
    } else {
      challenge.style.display = 'none';
    }
  });
}
</script>
</body>
</html>
<?php $conn->close(); ?>


