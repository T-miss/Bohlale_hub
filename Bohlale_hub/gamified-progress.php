<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ultimate Gamified Dashboard</title>
<style>
:root{
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --deep:#2b0028;
  --glass: rgba(255,255,255,0.14);
  --container-width:1100px;
  --radius:20px;
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* Base */
*{box-sizing:border-box;}
html,body{height:100%; margin:0; background: linear-gradient(180deg,var(--bg), #f7eef8 60%); color:var(--deep);}
body{-webkit-font-smoothing:antialiased; line-height:1.45;}
.container{width:min(94%,var(--container-width)); margin:0 auto; padding:20px;}

/* Header */
header{position:sticky; top:0; background:rgba(255,255,255,0.2); padding:20px; text-align:center; border-bottom:1px solid rgba(0,0,0,0.1); backdrop-filter: blur(10px);}
header h1{margin:0; color:var(--accent1); font-size:2.5rem;}

/* Stats and progress */
.stats{display:flex;flex-wrap:wrap;gap:18px;justify-content:center;margin-top:20px;}
.stat{background:var(--glass);padding:20px;border-radius:var(--radius);text-align:center;flex:1;min-width:150px;box-shadow:0 6px 18px rgba(0,0,0,0.08);}
.progress-container{background:var(--glass);border-radius:var(--radius);height:40px;overflow:hidden;margin-top:10px;}
.progress-bar{height:100%;width:0%;background:linear-gradient(135deg,var(--accent1),var(--accent2));text-align:center;color:white;font-weight:700;line-height:40px;transition:width 1s;}

/* Buttons */
.button{padding:12px 20px;border-radius:var(--radius);border:none;font-weight:600;cursor:pointer;margin:6px;transition:0.3s;}
.btn-primary{background:linear-gradient(135deg,var(--accent1),var(--accent2));color:white;}
.btn-primary:hover{opacity:0.9;transform:scale(1.05);}
.quick-buttons{display:flex;flex-wrap:wrap;gap:12px;margin-top:18px;justify-content:center;}

/* Badges */
.badge{background:var(--accent1);color:white;padding:8px 12px;border-radius:var(--radius);margin:4px;display:inline-block;cursor:pointer;transition:0.3s;animation:badgePop 0.5s;}
@keyframes badgePop{0%{transform:scale(0)}50%{transform:scale(1.2)}100%{transform:scale(1)}}

/* Histogram */
.histogram{margin-top:30px;}
.histogram canvas{max-width:100%;border-radius:var(--radius);background:white;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.1);}

/* Quiz modal */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);justify-content:center;align-items:center;z-index:1000;}
.modal-content{background:white;color:var(--deep);padding:20px;border-radius:var(--radius);max-width:500px;width:90%;text-align:center;}
.modal-content button{margin-top:12px;}

/* XP popup */
.xp-popup{position:fixed;top:20px;right:20px;background:linear-gradient(135deg,var(--accent1),var(--accent2));color:white;padding:12px 18px;border-radius:var(--radius);box-shadow:0 6px 18px rgba(0,0,0,0.25);animation:fadePopup 2s forwards;z-index:2000;}
@keyframes fadePopup{0%{opacity:0;transform:translateY(-20px)}10%{opacity:1;transform:translateY(0)}90%{opacity:1;transform:translateY(0)}100%{opacity:0;transform:translateY(-20px)}}

/* Footer */
footer{background:var(--accent2); color:white; text-align:center; padding:20px; margin-top:40px; border-radius:var(--radius);}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header>
  <h1>🎓 Ultimate Gamified Dashboard</h1>
</header>

<div class="container">

<!-- Stats -->
<div class="stats">
  <div class="stat"><h2 id="xpTotal">0</h2>XP</div>
  <div class="stat"><h2 id="level">Beginner</h2>Level</div>
  <div class="stat"><h2 id="coursesCompleted">0</h2>Courses Completed</div>
  <div class="stat"><h2 id="dailyStreak">0</h2>Daily Streak</div>
</div>

<!-- Progress bar -->
<div class="progress-container">
  <div class="progress-bar" id="levelBar">0%</div>
</div>

<!-- Quick action buttons -->
<div class="quick-buttons">
  <button class="button btn-primary" onclick="gainXP(50,'Quick Starter')">Quick Starter +50 XP</button>
  <button class="button btn-primary" onclick="gainXP(100,'First Course Complete')">First Course Complete +100 XP</button>
  <button class="button btn-primary" onclick="gainXP(150,'Daily Bonus')">Daily Bonus +150 XP</button>
  <button class="button btn-primary" onclick="openQuiz()">Take Quiz +200 XP</button>
</div>

<!-- Badges -->
<div class="badges-container" style="margin-top:20px;">
  <h3>🏆 Badges</h3>
  <div id="badges"></div>
</div>

<!-- XP histogram -->
<div class="histogram">
  <h3>📊 XP History</h3>
  <canvas id="xpChart" height="200"></canvas>
</div>

</div>

<!-- Footer -->
<footer>© 2025 Ultimate Gamified Dashboard. All rights reserved.</footer>

<!-- Quiz Modal -->
<div class="modal" id="quizModal">
  <div class="modal-content">
    <h2>📝 Quiz</h2>
    <p id="quizQuestion"></p>
    <div id="quizOptions"></div>
    <button class="button btn-primary" onclick="closeQuiz()">Close Quiz</button>
  </div>
</div>

<script>
// Data
let xp = 0;
let level = "Beginner";
let levelXP = [0,100,500,1000,2000]; 
let levelNames = ["Beginner","Intermediate","Advanced","Pro","Master"];
let xpHistory = [];
let badges = [];
let coursesCompleted = 0;
let dailyStreak = 0;
let lastBonusDate = null;
let quizData = [];

// Load Quiz from PHP
async function loadQuizData() {
    try {
        const response = await fetch('get_quiz.php');
        quizData = await response.json();
    } catch (err) {
        console.error("Failed to load quiz data:", err);
    }
}
loadQuizData();

// XP and Progress
function gainXP(amount,type){
    xp += amount;
    xpHistory.push({activity:type,xp:amount});
    if(type==="First Course Complete") coursesCompleted++;
    if(type==="Daily Bonus"){
        const today = new Date().toDateString();
        if(lastBonusDate!==today){dailyStreak++;lastBonusDate=today;}
    }
    showXPPopup(amount,type);
    document.getElementById("xpTotal").textContent = xp;
    document.getElementById("coursesCompleted").textContent = coursesCompleted;
    document.getElementById("dailyStreak").textContent = dailyStreak;
    updateLevel();
    updateProgressBar();
    updateChart();
    checkBadges(type);
}

// Level Update
function updateLevel(){
    for(let i=levelXP.length-1;i>=0;i--){
        if(xp>=levelXP[i]){level=levelNames[i]; break;}
    }
    document.getElementById("level").textContent = level;
}

// Progress Bar
function updateProgressBar(){
    let nextLevelXP = levelXP[levelNames.indexOf(level)+1]||levelXP[levelXP.length-1];
    let percent = Math.min(100,(xp/nextLevelXP)*100);
    document.getElementById("levelBar").style.width = percent+"%";
    document.getElementById("levelBar").textContent = Math.round(percent)+"%";
}

// Badges
function checkBadges(action){
    if(action=="Quick Starter" && !badges.includes("Quick Starter Badge")){ badges.push("Quick Starter Badge"); renderBadges(); }
    if(action=="First Course Complete" && !badges.includes("First Course Badge")){ badges.push("First Course Badge"); renderBadges(); }
    if(action=="Daily Bonus" && dailyStreak>=1 && !badges.includes("Daily Streak Badge")){ badges.push("Daily Streak Badge"); renderBadges(); }
    if(coursesCompleted>=3 && !badges.includes("Course Master")){ badges.push("Course Master"); renderBadges(); }
}
function renderBadges(){
    const container = document.getElementById("badges");
    container.innerHTML = badges.map(b=>`<div class="badge">${b}</div>`).join('');
}

// XP Popup
function showXPPopup(amount,activity){
    const popup = document.createElement('div');
    popup.className = 'xp-popup';
    popup.textContent = `+${amount} XP for ${activity}`;
    document.body.appendChild(popup);
    setTimeout(()=>popup.remove(),2000);
}

// Quiz
const quizModal = document.getElementById("quizModal");
const quizQuestion = document.getElementById("quizQuestion");
const quizOptions = document.getElementById("quizOptions");

function openQuiz(){
    if(quizData.length===0){ alert("Quiz questions not loaded!"); return; }
    quizModal.style.display="flex";
    const q = quizData[Math.floor(Math.random()*quizData.length)];
    quizQuestion.textContent = q.q;
    quizOptions.innerHTML = q.options.map((o,i)=>`<button class="button btn-primary" onclick="answerQuiz(${i},${q.a})">${o}</button>`).join('');
}
function answerQuiz(selected,correct){
    if(selected===correct){ alert("✅ Correct! +200 XP"); gainXP(200,"Quiz"); }
    else{ alert("❌ Wrong! Try next time."); }
    closeQuiz();
}
function closeQuiz(){ quizModal.style.display="none"; }

// XP Histogram
const ctx = document.getElementById('xpChart').getContext('2d');
const xpChart = new Chart(ctx,{type:'bar',data:{labels:[],datasets:[{label:'XP',data:[],backgroundColor:'rgba(255,43,125,0.7)',borderRadius:10}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
function updateChart(){
    xpChart.data.labels = xpHistory.map((_,i)=>"Activity "+(i+1));
    xpChart.data.datasets[0].data = xpHistory.map(h=>h.xp);
    xpChart.update();
}
</script>
</body>
</html>
