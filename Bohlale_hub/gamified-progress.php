<?php
session_start();
$student_id = 1;

$host="localhost"; $user="root"; $pass=""; $db="bohlale_hub";
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Get total XP and level
$level_res = $conn->query("SELECT total_xp, level_name FROM student_levels WHERE student_id=$student_id");
$level_row = $level_res->fetch_assoc() ?? ['total_xp'=>0,'level_name'=>'Beginner'];
$current_xp = $level_row['total_xp'];

// Fetch badges
$badges_res = $conn->query("SELECT badge_name FROM student_badges WHERE student_id=$student_id");
$badges = [];
while($row=$badges_res->fetch_assoc()) $badges[] = $row['badge_name'];

// Fetch completed courses
$courses_res = $conn->query("SELECT COUNT(DISTINCT track_id) as completed FROM student_progress WHERE student_id=$student_id AND completed=1");
$courses_completed = $courses_res->fetch_assoc()['completed'] ?? 0;

// XP thresholds for levels
$levels = [
    ['name'=>'Beginner','xp'=>0],
    ['name'=>'Intermediate','xp'=>1000],
    ['name'=>'Advanced Learner','xp'=>5000],
    ['name'=>'Master','xp'=>10000]
];

// Calculate next level
$next_level = array_filter($levels,function($l) use ($current_xp){ return $l['xp']>$current_xp; });
$next_level = $next_level ? array_values($next_level)[0] : end($levels);
$progress_percent = min(100, round(($current_xp/$next_level['xp'])*100));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gamified Progress</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<style>
body{font-family:'Inter',sans-serif;margin:0;background:#1a1a2e;color:#fff;}
.container{max-width:900px;margin:auto;padding:2rem;}
h1{text-align:center;color:#f0a500;}
.stats{display:flex;justify-content:space-around;margin-top:1rem;background:#162447;padding:1rem;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.3);}
.stat{text-align:center;}
.stat h2{margin:0;color:#f0a500;}
.progress-container{background:#0f3460;border-radius:15px;overflow:hidden;margin:1rem 0;height:30px;position:relative;}
.progress-bar{height:30px;background:linear-gradient(90deg,#f0a500,#e94560);width:0%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;transition:width 1s;}
.level-info{text-align:center;margin-top:1rem;}
.badges{display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:center;margin-top:1rem;}
.badge{background:#e94560;padding:0.5rem 1rem;border-radius:12px;font-weight:600;display:flex;align-items:center;gap:0.3rem;cursor:pointer;transition:0.3s;animation:pop 0.5s ease;}
.badge:hover{transform:scale(1.2);box-shadow:0 0 10px #f0a500;}
@keyframes pop{0%{transform:scale(0.5);opacity:0}100%{transform:scale(1);opacity:1}}
.upcoming{margin-top:2rem;text-align:center;color:#f0a500;}
.icon{width:20px;height:20px;display:inline-block;}
</style>
</head>
<body>
<div class="container">
<h1>🎮 Gamified Progress</h1>
<p style="text-align:center;color:#fff;">Track your academic journey through points, levels, and badges. Complete courses, earn XP, unlock levels and rewards!</p>

<div class="stats">
  <div class="stat">
    <h2 id="coursesCompleted"><?php echo $courses_completed; ?></h2>
    <p>Courses Completed</p>
  </div>
  <div class="stat">
    <h2 id="totalPoints"><?php echo $current_xp; ?></h2>
    <p>Points Total</p>
  </div>
  <div class="stat">
    <h2 id="badgesEarned"><?php echo count($badges); ?></h2>
    <p>Badges Earned</p>
  </div>
</div>

<div class="level-info">
  <p>Next Level: <strong><?php echo $next_level['name']; ?></strong> (<?php echo $next_level['xp']; ?> XP)</p>
  <div class="progress-container">
    <div class="progress-bar" id="levelBar"><?php echo $current_xp; ?> / <?php echo $next_level['xp']; ?> XP</div>
  </div>
</div>

<div class="badges">
<?php foreach($badges as $b): ?>
  <div class="badge"><span class="icon">🏆</span><?php echo htmlspecialchars($b); ?></div>
<?php endforeach; ?>
</div>

<div class="upcoming">
  <h3>Upcoming Rewards</h3>
  <p>Collaborator | Disciplined | Explorer</p>
</div>
</div>

<script>
// Animate progress bar
window.addEventListener('load',()=>{
    const bar = document.getElementById('levelBar');
    setTimeout(()=>{ bar.style.width='<?php echo $progress_percent; ?>%'; },200);
});

// Simulate gaining XP dynamically
function gainXP(amount){
    const totalPoints = document.getElementById('totalPoints');
    const bar = document.getElementById('levelBar');
    let currentXP = parseInt(totalPoints.textContent);
    const nextXP = <?php echo $next_level['xp']; ?>;
    currentXP += amount;
    totalPoints.textContent = currentXP;
    const percent = Math.min(100, Math.round((currentXP/nextXP)*100));
    bar.style.width = percent+'%';
    bar.textContent = currentXP+' / '+nextXP+' XP';

    if(currentXP >= nextXP){
        confetti({particleCount:200,spread:120,origin:{y:0.6}});
        alert('🎉 Level Up! You reached <?php echo $next_level['name']; ?>!');
    }
}

// Example: simulate earning 500 XP after 3 seconds
setTimeout(()=>{ gainXP(500); },3000);
</script>
</body>
</html>
