<?php
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch profiles
$result = $conn->query("SELECT * FROM profiles ORDER BY name ASC");
$profiles = [];
if($result) while($row = $result->fetch_assoc()) $profiles[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Networking</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
/* Original styles + role badges */
main { padding: 2rem; font-family: 'Inter', sans-serif; background: #f9f9f9; }
.features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
.features p { margin-bottom: 1rem; color: #555; }
.search-bar { margin-bottom: 1rem; }
.search-bar input { width: 100%; padding: 0.5rem 1rem; border-radius: 12px; border: 1px solid #ccc; font-size: 1rem; }
.filter-btns { margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap; }
.filter-btns button { padding: 0.4rem 1rem; border: none; border-radius: 10px; background: #ff2b7d; color: #fff; cursor: pointer; transition: background 0.2s; }
.filter-btns button:hover { background: #b14b7f; }
.profile-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
.profile-card { background: #fff; border-radius: 15px; padding: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; text-align: center; position: relative; }
.profile-card:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
.profile-card img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 0.5rem; }
.profile-card h3 { margin: 0.3rem 0; font-size: 1.1rem; color: #333; }
.profile-card p { font-size: 0.9rem; color: #666; margin: 0.2rem 0; }
.connect-btn { margin-top: 0.5rem; padding: 0.4rem 1rem; background: #007bff; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
.connect-btn:hover { background: #0056b3; }
.role-badge { position: absolute; top: 10px; right: 10px; padding: 0.3rem 0.6rem; border-radius: 8px; font-size: 0.75rem; font-weight: 500; color: #fff; }
.peer { background-color: #ff6f61; }
.mentor { background-color: #6f42c1; }
.professional { background-color: #28a745; }
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
<h2>Networking 🤝</h2>
<p>Connect with peers, mentors, and professionals in your field for guidance, support, and opportunities.</p>

<div class="search-bar">
<input type="text" id="searchInput" placeholder="Search by name or role..." onkeyup="searchProfiles()">
</div>

<div class="filter-btns">
<button onclick="filterProfiles('all')">All</button>
<button onclick="filterProfiles('peer')">Peers</button>
<button onclick="filterProfiles('mentor')">Mentors</button>
<button onclick="filterProfiles('professional')">Professionals</button>
</div>

<div class="profile-list" id="profileList">
<?php foreach($profiles as $p): ?>
<div class="profile-card" data-type="<?= $p['role'] ?>">
  <span class="role-badge <?= $p['role'] ?>"><?= ucfirst($p['role']) ?></span>
  <img src="<?= $p['avatar'] ?>" alt="Profile Picture">
  <h3><?= $p['name'] ?></h3>
  <p><?= $p['description'] ?></p>
  <button class="connect-btn" onclick="connectProfile(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">Connect</button>
</div>
<?php endforeach; ?>
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

// Filter profiles
function filterProfiles(type) {
  document.querySelectorAll('.profile-card').forEach(c => {
    c.style.display = (type === 'all' || c.dataset.type === type) ? 'block' : 'none';
  });
}

// Search profiles
function searchProfiles() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.profile-card').forEach(c => {
    const name = c.querySelector('h3').textContent.toLowerCase();
    const role = c.querySelector('.role-badge').textContent.toLowerCase();
    c.style.display = (name.includes(q) || role.includes(q)) ? 'block' : 'none';
  });
}

// Connect button AJAX
function connectProfile(profileId, profileName) {
  const userName = "Tshiamo Winnie"; // replace with login session
  fetch('connect.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `profile_id=${profileId}&user_name=${encodeURIComponent(userName)}`
  })
  .then(() => alert(`You have sent a connection request to ${profileName}!`))
  .catch(err => console.error(err));
}
</script>
</body>
</html>

