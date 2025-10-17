<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Timetable</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#EDEAFF;
  --accent1:#6C63FF;
  --accent2:#FF6FB5;
  --accent3:#FFA75C;
  --deep:#1E1B3A;
  --glass: rgba(255,255,255,0.2);
  --container-width:1100px;
  --radius:20px;
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

body{
  margin:0;
  background: linear-gradient(180deg,#EDEAFF 0%, #FDFDFF 40%, #E4E0FF 100%);
  color: var(--deep);
  line-height:1.45;
}

.container{width:min(94%,var(--container-width)); margin:0 auto;}

.site-header{
  position:sticky; top:0;
  background: var(--glass);
  backdrop-filter: blur(12px);
  padding:12px 0; 
  z-index:40;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.header-inner{display:flex; align-items:center; justify-content:space-between; padding:18px 0;}
.brand{display:flex; gap:12px; align-items:center;}
.logo-mark{
  width:50px; height:50px; border-radius:12px;
  background: linear-gradient(135deg,var(--accent1),var(--accent2),var(--accent3));
  box-shadow: 0 4px 12px rgba(108,99,255,0.25);
}
.brand-title{font-weight:700; font-size:1.4rem; color: var(--accent1); text-shadow:1px 1px 2px rgba(0,0,0,0.15);}

.main-nav{display:flex; gap:20px;}
.main-nav a{
  color: var(--deep);
  text-decoration:none;
  font-weight:600;
  padding:10px 12px;
  border-radius:12px;
  transition: all 0.3s ease;
}
.main-nav a:hover{
  background: var(--accent2);
  color:#fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255,111,181,0.3);
}

main{padding:2rem;}
.features h2{
  font-size:2rem;
  margin-bottom:0.5rem;
  display:flex; align-items:center; gap:0.5rem;
  color: var(--accent1);
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}
.features p{margin-bottom:1rem; color:#555;}

.wall-posts{display:flex; flex-direction:column; gap:1rem;}
.post{
  background: linear-gradient(135deg, #fff, #f0f0ff);
  padding:1rem; border-radius:12px;
  box-shadow:0 5px 15px rgba(108,99,255,0.2);
  position:relative;
  transition: transform 0.3s, box-shadow 0.3s;
}
.post:hover{
  transform: translateY(-3px);
  box-shadow:0 8px 20px rgba(108,99,255,0.3);
}
.post h4{margin:0 0 0.5rem 0; color: var(--accent1);}
.post p{margin:0;}
.like-btn{
  position:absolute; top:1rem; right:1rem;
  background: var(--accent2); color:#fff;
  border:none; border-radius:6px; padding:0.2rem 0.5rem;
  cursor:pointer; font-size:0.8rem;
  transition: 0.3s;
}
.like-btn.liked{background: var(--accent1);}

.new-post{margin-top:2rem; display:flex; flex-direction:column; gap:0.5rem;}
.new-post input{padding:0.5rem; border-radius:8px; border:1px solid #ccc; width:100%; font-size:1rem;}
.new-post button{
  align-self:flex-start; background:var(--accent1);
  color:#fff; padding:0.5rem 1rem; border:none; border-radius:8px;
  cursor:pointer; transition: 0.3s;
}
.new-post button:hover{background:var(--accent2);}

.site-footer{
  margin-top:40px; padding:28px 0; text-align:center;
  color:#fff; background: linear-gradient(90deg,var(--accent1),var(--accent2),var(--accent3));
  font-weight:600;
}
</style>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <div class="logo-mark"></div>
      <span class="brand-title">Student Portal</span>
    </div>
    <nav class="main-nav">
      <a href="#">Home</a>
      <a href="#">Features</a>
      <a href="#">Timetable</a>
    </nav>
  </div>
</header>

<main class="container">
  <section class="features">
    <h2>Smart Timetable 🗓️</h2>
    <p>Organize your classes, assignments, and study sessions in one interactive timetable.</p>

    <div class="wall-posts" id="timetablePosts">
      <div class="post">
        <h4>Monday</h4>
        <p>09:00 - 10:30: Programming 101</p>
        <button class="like-btn" onclick="toggleHighlight(this)">Highlight</button>
      </div>
      <div class="post">
        <h4>Tuesday</h4>
        <p>11:00 - 12:30: Database Management</p>
        <button class="like-btn" onclick="toggleHighlight(this)">Highlight</button>
      </div>
    </div>

    <div class="new-post">
      <input type="text" id="day" placeholder="Day of the Week">
      <input type="text" id="time" placeholder="Time (e.g., 09:00 - 10:30)">
      <input type="text" id="subject" placeholder="Subject / Activity">
      <button onclick="addTimetableEntry()">Add Entry</button>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom">
    <small>© <span id="year"></span> Student Portal</small>
  </div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

function toggleHighlight(btn) {
  btn.classList.toggle('liked');
  btn.textContent = btn.classList.contains('liked') ? 'Highlighted' : 'Highlight';
}

function addTimetableEntry() {
  const day = document.getElementById('day').value.trim();
  const time = document.getElementById('time').value.trim();
  const subject = document.getElementById('subject').value.trim();
  
  if(!day || !time || !subject){
    alert("Please enter day, time, and subject.");
    return;
  }

  const wall = document.getElementById('timetablePosts');
  const post = document.createElement('div');
  post.className = 'post';
  post.innerHTML = `
    <h4>${day}</h4>
    <p>${time}: ${subject}</p>
    <button class="like-btn" onclick="toggleHighlight(this)">Highlight</button>
  `;
  wall.prepend(post);

  document.getElementById('day').value='';
  document.getElementById('time').value='';
  document.getElementById('subject').value='';
}
</script>

</body>
</html>
