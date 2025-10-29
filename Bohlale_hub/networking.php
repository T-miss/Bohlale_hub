<?php
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch profiles
$result = $conn->query("SELECT * FROM profiles ORDER BY name ASC");
$profiles = [];
if($result) while($row = $result->fetch_assoc()) $profiles[] = $row;

// Handle Connect AJAX
if(isset($_POST['connect']) && isset($_POST['profile_id'])){
    $user = $_POST['user_name'];
    $profile_id = $_POST['profile_id'];
    $stmt = $conn->prepare("INSERT INTO connections (user_name, profile_id) VALUES (?, ?)");
    $stmt->bind_param("si", $user, $profile_id);
    $stmt->execute();
    echo "success";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Networking</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --deep:#2b0028;
  --glass: rgba(255,255,255,0.18);
  --container-width:1100px;
  --radius:20px;
  font-family: 'Inter', sans-serif;
}

/* Base */
*{box-sizing:border-box;}
body{margin:0; background:linear-gradient(180deg,var(--bg), #f7eef8 60%); color:var(--deep); min-height:100vh; display:flex; flex-direction:column;}
.container{width:min(94%,var(--container-width)); margin:0 auto;}

/* Header */
.site-header{position:sticky; top:0; z-index:40; padding:12px 0; background:var(--glass); border-radius:var(--radius); backdrop-filter:blur(12px); margin-bottom:1rem;}
.header-inner{display:flex; justify-content:space-between; align-items:center;}
.brand{display:flex; align-items:center; gap:10px;}
.logo-mark{width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,var(--accent1),var(--accent2));}
.brand-title{font-weight:700; color:var(--deep);}
.main-nav a{color:var(--deep); text-decoration:none; font-weight:600; padding:8px 6px; border-radius:8px;}
.main-nav a:hover{background: rgba(255,255,255,0.4);}
.burger{display:none; background:transparent; border:0; cursor:pointer;}
.burger span{display:block; width:26px; height:3px; background:var(--deep); margin:5px 0; border-radius:3px;}
.mobile-menu{display:none; background:var(--glass); padding:12px; border-radius:12px;}
.mobile-menu a{display:block;color:var(--deep); padding:8px 0; font-weight:700; text-decoration:none;}

/* Features */
main{flex:1; padding:2rem 0;}
.features h2{font-size:2rem; margin-bottom:0.5rem; color: var(--deep);}
.features p{color:rgba(50,0,50,0.75); margin-bottom:1rem;}

/* Search + Filter */
.search-bar input{
  width:180px; 
  padding:0.4rem 0.8rem; 
  border-radius:12px; 
  border:1px solid #ccc; 
  font-size:0.9rem;
}
.filter-btns{display:flex; gap:0.5rem; margin:1rem 0; flex-wrap:wrap;}
.filter-btns button{padding:0.3rem 0.8rem; border:none; border-radius:12px; background:linear-gradient(135deg,var(--accent1),var(--accent2)); color:#fff; cursor:pointer; font-weight:600; transition: transform 0.2s;}
.filter-btns button:hover{transform:translateY(-2px) scale(1.05);}

/* Profile Grid */
.profile-list{display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; position:relative; z-index:2;}
.profile-card{
  background:var(--glass); 
  backdrop-filter:blur(12px); 
  border-radius:var(--radius); 
  padding:1rem; 
  text-align:center; 
  box-shadow:0 6px 24px rgba(60,10,60,0.08); 
  transition: transform 0.25s, box-shadow 0.25s; 
  position:relative; 
  overflow:hidden;
}
.profile-card:hover{transform:translateY(-5px); box-shadow:0 10px 28px rgba(60,10,60,0.15);}

/* Profile Images + Mimic Miniature */
.profile-card .avatar{
  width:80px; height:80px; margin:0 auto 0.5rem; border-radius:50%; overflow:hidden; background:#fff; padding:5px;
}
.profile-card .mini-thumbs{display:flex; justify-content:center; gap:4px; margin-top:5px;}
.profile-card .mini-thumbs img{width:24px; height:24px; border-radius:50%; border:1px solid rgba(255,255,255,0.2); transition: transform 0.2s;}
.profile-card .mini-thumbs img:hover{transform:scale(1.2);}

/* User Menu Bars */
.user-menu{position:absolute; top:8px; left:8px; display:flex; flex-direction:column; gap:4px;}
.user-menu button{padding:3px 6px; font-size:0.7rem; border:none; border-radius:6px; cursor:pointer; background:rgba(255,255,255,0.15); color:var(--deep); transition: background 0.2s;}
.user-menu button:hover{background:rgba(255,255,255,0.3);}

/* Role Badges */
.role-badge{position:absolute; top:8px; right:8px; padding:0.3rem 0.6rem; border-radius:10px; font-size:0.7rem; font-weight:600; color:white; cursor:default;}
.peer{background:#ff6f61;}
.mentor{background:#6f42c1;}
.professional{background:#28a745;}

/* Connect Button */
.connect-btn{margin-top:0.5rem; padding:0.4rem 0.8rem; background:linear-gradient(135deg,var(--accent1),var(--accent2)); color:#fff; border:none; border-radius:12px; cursor:pointer; font-weight:600; font-size:0.85rem; transition: transform 0.2s, box-shadow 0.2s;}
.connect-btn:hover{transform:translateY(-2px) scale(1.05); box-shadow:0 6px 16px rgba(177,75,127,0.25);}

/* Footer */
.site-footer{margin-top:auto; padding:1.5rem 1rem; text-align:center; color:white; background:linear-gradient(135deg,var(--accent1),var(--accent2)); font-size:0.85rem;}
.site-footer small{color:white;}
</style>
</head>
<body>

<header class="site-header">
<div class="container header-inner">
  <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
  <nav class="main-nav">
    <a href="dashboard.php">Home</a>
    <button class="burger" id="burgerBtn"><span></span><span></span><span></span></button>
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
<div class="profile-card" data-type="<?= $p['role'] ?>" data-id="<?= $p['id'] ?>">
  <!-- User menu -->
  <div class="user-menu">
    <button onclick="alert('Message <?= addslashes($p['name']) ?>')">Message</button>
    <button onclick="alert('View Profile <?= addslashes($p['name']) ?>')">View</button>
    <button onclick="alert('Report <?= addslashes($p['name']) ?>')">Report</button>
  </div>
  <span class="role-badge <?= $p['role'] ?>"><?= ucfirst($p['role']) ?></span>
  <!-- Main Avatar: Facebook-style black & white -->
  <div class="avatar">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
  </div>
  <h3><?= $p['name'] ?></h3>
  <p><?= $p['description'] ?></p>
  <!-- Mimic Miniatures -->
  <div class="mini-thumbs">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
  </div>
  <button class="connect-btn" onclick="connectProfile(<?= $p['id'] ?>,'<?= addslashes($p['name']) ?>',this)">Connect</button>
</div>
<?php endforeach; ?>
</div>
</section>
</main>

<footer class="site-footer">
<div class="container footer-bottom"><small>© <span id="year"></span> Bohlale hub</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();
const burgerBtn = document.getElementById('burgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('is-open'));

// Filter profiles
function filterProfiles(type){
  document.querySelectorAll('.profile-card').forEach(c=>{
    c.style.display=(type==='all'||c.dataset.type===type)?'block':'none';
  });
}

// Search profiles
function searchProfiles(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.profile-card').forEach(c=>{
    const name=c.querySelector('h3').textContent.toLowerCase();
    const role=c.querySelector('.role-badge').textContent.toLowerCase();
    c.style.display=(name.includes(q)||role.includes(q))?'block':'none';
  });
}

// Connect AJAX
function connectProfile(profileId, profileName, btn){
  const userName="Tshiamo Winnie"; // Replace with session login
  fetch('',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`connect=1&profile_id=${profileId}&user_name=${encodeURIComponent(userName)}`
  }).then(r=>r.text()).then(r=>{
    if(r==='success'){
      btn.textContent="Pending";
      btn.disabled=true;
      btn.style.background="gray";
    }
  }).catch(e=>console.error(e));
}
</script>

</body>
</html>
