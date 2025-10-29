<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Timetable</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --accent3:#ff6aa6;
  --deep:#2b0028;
  --glass: rgba(255,255,255,0.18);
  --container-width:1100px;
  --radius:18px;
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

body{
  margin:0;
  background: linear-gradient(180deg,var(--bg), #f7eef8 60%);
  color: var(--deep);
  line-height:1.45;
  display:flex;
  flex-direction:column;
  min-height:100vh;
}

.container{width:min(94%,var(--container-width)); margin:0 auto;}

/* HEADER */
.site-header{
  position:sticky; top:0;
  background: var(--glass);
  backdrop-filter: blur(12px);
  padding:12px 0;
  z-index:40;
  box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}
.header-inner{display:flex; align-items:center; justify-content:space-between; padding:12px 0;}
.brand{display:flex; gap:12px; align-items:center;}
.logo-mark{
  width:50px; height:50px; border-radius:12px;
  background: linear-gradient(135deg,var(--accent1),var(--accent2),var(--accent3));
  box-shadow: 0 6px 18px rgba(177,75,127,0.25);
}
.brand-title{font-weight:700; font-size:1.4rem; color: var(--accent1);}
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
}

/* TIMETABLE CONTAINER */
.timetable-container{
  display:flex;
  overflow-x:auto;
  margin-top:2rem;
  gap:12px;
  padding-bottom:12px;
}
.timetable-column{
  min-width:180px;
  display:flex;
  flex-direction:column;
  gap:8px;
}
.timetable-column-header{
  font-weight:700;
  text-align:center;
  padding:8px 4px;
  color: var(--accent1);
  background: var(--glass);
  border-radius: var(--radius);
}

/* TIME SLOTS */
.time-slot{
  position:relative;
  min-height:60px;
  border-radius: var(--radius);
  background: var(--glass);
  backdrop-filter: blur(12px);
  padding:4px;
  transition: transform 0.3s, box-shadow 0.3s;
}
.time-slot:hover{
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(43,0,40,0.2);
}
.time-label{
  font-size:0.7rem;
  font-weight:500;
  color: rgba(43,0,40,0.7);
}
.subject{
  margin-top:6px;
  padding:6px 8px;
  border-radius:12px;
  color:#fff;
  font-weight:600;
  font-size:0.85rem;
  cursor:pointer;
  display:flex;
  justify-content:space-between;
  align-items:center;
  transition: transform 0.2s;
}
.subject:hover{transform: scale(1.03);}
.subject.programming{background: linear-gradient(135deg,var(--accent1),var(--accent3));}
.subject.database{background: linear-gradient(135deg,var(--accent2),var(--accent3));}
.subject.maths{background: linear-gradient(135deg,var(--accent1),var(--accent2));}

/* HIGHLIGHT & DELETE */
.subject.highlighted{box-shadow:0 4px 14px rgba(255,106,166,0.4);}
.delete-btn{
  background: rgba(255,255,255,0.3);
  border:none;
  border-radius:6px;
  cursor:pointer;
  padding:2px 5px;
  font-size:0.8rem;
  margin-left:4px;
}
.delete-btn:hover{background: rgba(255,255,255,0.6);}

/* NEW ENTRY FORM */
.new-entry{
  margin-top:2rem;
  display:flex;
  flex-wrap:wrap;
  gap:0.5rem;
}
.new-entry input, .new-entry select{
  padding:0.5rem; border-radius:10px; border:1px solid #ccc; font-size:1rem;
}
.new-entry button{
  padding:0.5rem 1rem; border-radius:12px; border:none; background: var(--accent1); color:#fff; cursor:pointer; transition:0.3s;
}
.new-entry button:hover{background: var(--accent2);}

/* FOOTER */
.site-footer{
  margin-top:auto;
  padding:28px 0;
  text-align:center;
  color:#fff;
  background: linear-gradient(90deg,var(--accent1),var(--accent2),var(--accent3));
  font-weight:600;
  border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);
}

/* Responsive */
@media(max-width:768px){
  .timetable-container{flex-direction:column; gap:16px;}
}
</style>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <div class="logo-mark"></div>
      <span class="brand-title">Smart Timetable</span>
    </div>
    <nav class="main-nav">
      <a href="dashboard.php">Home</a>

      
    </nav>
  </div>
</header>

<main class="container">
  <h2> 🗓️Smart Timetable</h2>
  <p>Add subjects to any date you wish. Click a subject to highlight, or delete it.</p>

  <div class="timetable-container" id="timetable">
    <!-- Columns will be dynamically added here -->
  </div>

  <div class="new-entry">
    <input type="date" id="date">
    <input type="text" id="time" placeholder="Time e.g. 14:00-15:30">
    <input type="text" id="subject" placeholder="Subject Name">
    <select id="type">
      <option value="">Type</option>
      <option value="programming">Programming</option>
      <option value="database">Database</option>
      <option value="maths">Maths</option>
    </select>
    <button onclick="addSubject()">Add Subject</button>
  </div>
</main>

<footer class="site-footer">
  <div class="container">
    <small>© <span id="year"></span> Smart Timetable</small>
  </div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Highlight toggle
function highlight(el){
  el.classList.toggle('highlighted');
}

// Delete a subject
function deleteSubject(e){
  e.stopPropagation();
  e.target.parentElement.remove();
}

// Add subject dynamically based on selected date
function addSubject(){
  const dateValue = document.getElementById('date').value;
  const time = document.getElementById('time').value.trim();
  const subject = document.getElementById('subject').value.trim();
  const type = document.getElementById('type').value;

  if(!dateValue || !time || !subject || !type){
    alert("Fill all fields");
    return;
  }

  const timetable = document.getElementById('timetable');
  let col = Array.from(timetable.children).find(c => c.dataset.date === dateValue);

  // Create column if it doesn't exist
  if(!col){
    col = document.createElement('div');
    col.className='timetable-column';
    col.dataset.date = dateValue;
    const dateObj = new Date(dateValue);
    const options = { weekday:'long', day:'numeric', month:'short' };
    const header = document.createElement('div');
    header.className='timetable-column-header';
    header.textContent = dateObj.toLocaleDateString(undefined, options);
    col.appendChild(header);
    timetable.appendChild(col);
  }

  // Add subject
  const slot = document.createElement('div');
  slot.className='time-slot';
  slot.innerHTML = `<div class="time-label">${time}</div>
                    <div class="subject ${type}" onclick="highlight(this)">
                      ${subject} 
                      <button class="delete-btn" onclick="deleteSubject(event)">✕</button>
                    </div>`;
  col.appendChild(slot);

  // Clear inputs
  document.getElementById('time').value='';
  document.getElementById('subject').value='';
  document.getElementById('type').value='';
  document.getElementById('date').value='';
}
</script>

</body>
</html>
