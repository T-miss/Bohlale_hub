<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Intelligent Tutoring</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
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

body{
  margin:0;
  background: linear-gradient(180deg,var(--bg), #f7eef8 60%);
  color: var(--deep);
  display:flex;
  flex-direction:column;
  min-height:100vh;
}

header{
  position:sticky;
  top:0;
  background:var(--glass);
  padding:1rem 2rem;
  backdrop-filter: blur(10px);
  display:flex;
  align-items:center;
  gap:12px;
  z-index:10;
}
header .back{
  cursor:pointer;
  font-size:1.5rem;
  font-weight:700;
  color:var(--accent1);
}
header h1{margin:0; font-size:1.6rem; font-weight:700; color:var(--deep);}

main.container{
  flex:1;
  width:min(94%, var(--container-width));
  margin:2rem auto;
}

.features h2{
  font-size:2rem;
  color:var(--accent1);
  margin-bottom:0.5rem;
}
.features p{
  background:var(--accent1);
  color:#fff;
  padding:0.5rem 1rem;
  border-radius:12px;
  display:inline-block;
  margin-bottom:1rem;
}

.question-container{margin-top:1.5rem;}
.question{
  background:#fff;
  padding:1.5rem;
  border-radius:var(--radius);
  box-shadow:0 4px 15px rgba(0,0,0,0.1);
  margin-bottom:1rem;
  transition: transform 0.3s;
}
.question h3{margin:0 0 0.5rem 0; color:var(--accent1);}
.hint{color:var(--accent2); cursor:pointer; text-decoration:underline; display:inline-block; margin-top:0.5rem;}
.hint-text{display:none; color:#555; margin-top:0.5rem; font-style:italic;}
.student-input{
  width:100%;
  padding:0.6rem;
  margin-top:0.5rem;
  border-radius:12px;
  border:1px solid #ccc;
}
.btn{
  padding:0.6rem 1rem;
  border:none;
  border-radius:12px;
  cursor:pointer;
  font-weight:600;
  transition:0.2s;
  margin-top:0.5rem;
}
.btn-check{background:var(--accent2); color:#fff;}
.btn-check:hover{background:var(--accent1);}
.btn-next{background:#28a745; color:#fff;}
.btn-next:hover{background:#218838;}
.progress-container{
  background:#e0e0e0;
  border-radius:20px;
  overflow:hidden;
  height:24px;
  margin-top:1rem;
}
.progress-bar{
  height:24px;
  width:0%;
  background:var(--accent1);
  color:#fff;
  text-align:center;
  line-height:24px;
  font-weight:600;
  transition:width 0.5s;
}
footer{
  text-align:center;
  padding:1.5rem;
  background:var(--accent2);
  color:#fff;
  border-radius:var(--radius);
  margin-top:auto;
}
</style>
</head>
<body>

<header>
  
  <h1>Bohlale hub</h1>
</header>

<main class="container features">
  <h2>AI-Powered Tutoring 🧠</h2>
  <p>Answer each question. Use hints if needed. Only proceed after a correct answer!</p>
  <button class="btn btn-check" onclick="startTutoring()">Start Tutoring</button>

  <div class="question-container" id="questionContainer"></div>

  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>
</main>

<footer class="site-footer">
  <div>© <span id="year"></span> Student Portal</div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

function goBack(){ window.location.href='dashboard.php'; }

// Sample questions
let questions = [
  {id:1, question_text:"What is 2 + 2?", answer_text:"4", hint_text:"It's the same as 2*2."},
  {id:2, question_text:"What is the capital of France?", answer_text:"Paris", hint_text:"It's also called the city of love."},
  {id:3, question_text:"Which color mixes with blue to make green?", answer_text:"Yellow", hint_text:"Think primary colors."},
  {id:4, question_text:"What gas do we breathe in?", answer_text:"Oxygen", hint_text:"Essential for life."},
  {id:5, question_text:"How many continents are there?", answer_text:"7", hint_text:"Between 5 and 10."},
  
  // 10 extra questions
  {id:6, question_text:"What is the largest planet in our solar system?", answer_text:"Jupiter", hint_text:"It's a gas giant."},
  {id:7, question_text:"Who wrote 'Romeo and Juliet'?", answer_text:"Shakespeare", hint_text:"Famous English playwright."},
  {id:8, question_text:"What is H2O commonly known as?", answer_text:"Water", hint_text:"Essential for life."},
  {id:9, question_text:"What is 10 squared?", answer_text:"100", hint_text:"10*10."},
  {id:10, question_text:"Which ocean is the largest?", answer_text:"Pacific", hint_text:"It covers more than 30% of Earth's surface."},
  {id:11, question_text:"What is the freezing point of water in Celsius?", answer_text:"0", hint_text:"At 0 degrees it turns to ice."},
  {id:12, question_text:"What is the chemical symbol for gold?", answer_text:"Au", hint_text:"It comes from the Latin 'Aurum'."},
  {id:13, question_text:"How many hours are in a day?", answer_text:"24", hint_text:"Think of the rotation of Earth."},
  {id:14, question_text:"Which planet is known as the Red Planet?", answer_text:"Mars", hint_text:"It's the fourth planet from the Sun."},
  {id:15, question_text:"What is the largest mammal?", answer_text:"Blue Whale", hint_text:"It lives in the ocean and can grow up to 30 meters long."}
];

let currentQuestion = 0;
let studentAnswers = {};

function startTutoring(){
  currentQuestion = 0;
  showQuestion();
}

function showQuestion(){
  const container = document.getElementById('questionContainer');
  container.innerHTML = '';
  const q = questions[currentQuestion];
  const div = document.createElement('div');
  div.className='question';
  div.innerHTML=`
    <h3>Q${currentQuestion+1}: ${q.question_text}</h3>
    <span class="hint" onclick="toggleHint(this)">Show Hint</span>
    <div class="hint-text">${q.hint_text}</div>
    <input type="text" class="student-input" placeholder="Type your answer here..." value="${studentAnswers[q.id]||''}">
    <button class="btn btn-check" onclick="checkAnswer(this)">Check Answer</button>
    <button class="btn btn-next" style="display:none" onclick="nextQuestion()">Next Question →</button>
  `;
  container.appendChild(div);
}

function toggleHint(elem){
  const hint = elem.nextElementSibling;
  if(hint.style.display==='block'){ hint.style.display='none'; elem.textContent='Show Hint'; }
  else{ hint.style.display='block'; elem.textContent='Hide Hint'; }
}

function checkAnswer(btn){
  const parent = btn.parentElement;
  const input = parent.querySelector('.student-input');
  const studentAnswer = input.value.trim();
  const correctAnswer = questions[currentQuestion].answer_text.trim();
  
  studentAnswers[questions[currentQuestion].id] = studentAnswer;

  if(studentAnswer.toLowerCase()===correctAnswer.toLowerCase()){
    btn.disabled=true;
    parent.querySelector('.btn-next').style.display='inline-block';
    triggerConfetti();
    updateProgress();
  }else{
    alert('Incorrect. Try again or use the hint.');
  }
}

function nextQuestion(){
  currentQuestion++;
  if(currentQuestion<questions.length){ showQuestion(); }
  else{ alert('🎉 All questions completed!'); }
  updateProgress();
}

function updateProgress(){
  const percent = Math.round((currentQuestion / questions.length)*100);
  const bar = document.getElementById('progressBar');
  bar.style.width = percent + '%';
  bar.textContent = percent + '%';
}

function triggerConfetti(){
  confetti({
    particleCount: 100,
    spread: 70,
    origin: { y: 0.6 }
  });
}
</script>
</body>
</html>
