<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Intelligent Tutoring</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; }
header { display: flex; align-items: center; padding: 1rem; background: #007bff; color: #fff; }
header .back { cursor: pointer; margin-right: 1rem; font-size: 1.5rem; }
main { padding: 2rem; }
.features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
.features p { margin-bottom: 1rem; color: #fff; background: #007bff; padding: 0.5rem 1rem; border-radius: 8px; display: inline-block; }
.question-container { margin-top: 1rem; }
.question { background: #fff; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.question h3 { margin: 0 0 0.5rem 0; color: #007bff; }
.hint { color: #28a745; cursor: pointer; text-decoration: underline; display: inline-block; margin-top: 0.5rem; }
.hint-text { display: none; color: #555; margin-top: 0.5rem; font-style: italic; }
.answer { margin-top: 0.5rem; color: #333; font-weight: 500; display: none; }
.student-input { width: 100%; padding: 0.5rem; margin-top: 0.5rem; border-radius: 8px; border: 1px solid #ccc; }
.btn { padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s; }
.btn-start { background: #007bff; color: #fff; margin-top: 1rem; }
.btn-start:hover { background: #0056b3; }
.btn-complete { background: #28a745; color: #fff; margin-top: 0.5rem; }
.btn-complete:hover { background: #218838; }
.btn-check { background: #ffc107; color: #fff; margin-top: 0.5rem; }
.btn-check:hover { background: #e0a800; }
.btn-download { background: #6f42c1; color: #fff; margin-top: 1rem; }
.btn-download:hover { background: #5936a2; }
.progress-container { background: #e0e0e0; border-radius: 15px; overflow: hidden; margin-top: 1rem; }
.progress-bar { height: 20px; background: #28a745; width: 0%; text-align: center; color: #fff; line-height: 20px; font-size: 0.9rem; transition: width 0.5s; }
</style>
</head>
<body>

<header>
  <span class="back" onclick="goBack()">←</span>
  <h1>Intelligent Tutoring</h1>
</header>

<main>
<section class="features">
  <h2>AI-Powered Tutoring 🧠</h2>
  <p>Answer the questions below. Use hints if needed. Check your answers and track your progress.</p>
  <button class="btn btn-start" onclick="startTutoring()">Start Tutoring</button>

  <div class="question-container" id="questionContainer" style="display:none;"></div>

  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>

  <button class="btn btn-download" onclick="downloadSession()">Download Session Summary</button>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Back button
function goBack() { window.location.href = "dashboard.php"; }

// Load questions and previous answers
async function startTutoring() {
  const container = document.getElementById('questionContainer');
  container.innerHTML = '';
  container.style.display = 'block';

  const response = await fetch('fetch_questions.php');
  const questions = await response.json();

  const savedResponse = await fetch('load_answers.php');
  const savedAnswers = await savedResponse.json();

  questions.forEach((item, index) => {
    const div = document.createElement('div');
    div.className = 'question';
    div.dataset.id = item.id;

    let studentAnswer = savedAnswers[item.id] ? savedAnswers[item.id].student_answer : '';
    let completed = savedAnswers[item.id] ? savedAnswers[item.id].completed : 0;

    div.innerHTML = `
      <h3>Q${index+1}: ${item.question_text}</h3>
      <span class="hint" onclick="toggleHint(this)">Show Hint</span>
      <div class="hint-text">${item.hint_text}</div>
      <input type="text" class="student-input" placeholder="Type your answer here..." value="${studentAnswer}">
      <button class="btn btn-check" onclick="checkAnswer(this)" ${completed ? 'disabled' : ''}>Check Answer</button>
      <div class="answer">${item.answer_text}</div>
      <button class="btn btn-complete" onclick="completeQuestion(this)" style="display:${completed ? 'inline-block' : 'none'};">Mark Complete</button>
    `;
    container.appendChild(div);
  });

  updateProgress();
}

// Toggle hint visibility
function toggleHint(elem) {
  const hintText = elem.nextElementSibling;
  if(hintText.style.display === 'block') { hintText.style.display = 'none'; elem.textContent = 'Show Hint'; }
  else { hintText.style.display = 'block'; elem.textContent = 'Hide Hint'; }
}

// Check student answer and save in real time
function checkAnswer(btn) {
  const parent = btn.parentElement;
  const input = parent.querySelector('.student-input');
  const answerDiv = parent.querySelector('.answer');
  const questionId = parent.dataset.id;
  const studentAnswer = input.value.trim();
  const correct = studentAnswer.toLowerCase() === answerDiv.textContent.trim().toLowerCase();

  // Mark completed if correct
  let completed = correct ? 1 : 0;

  // Save to database via AJAX
  fetch('save_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `question_id=${questionId}&student_answer=${encodeURIComponent(studentAnswer)}&completed=${completed}`
  });

  if(correct) {
    alert('Correct! ✅');
    parent.querySelector('.btn-complete').style.display = 'inline-block';
    btn.disabled = true;
  } else {
    alert('Incorrect. Try again or use the hint.');
  }

  updateProgress();
}

// Mark question complete manually
function completeQuestion(btn) {
  btn.parentElement.style.opacity = 0.6;
  btn.disabled = true;
  updateProgress();
}

// Update progress bar
function updateProgress() {
  const questionsDiv = document.querySelectorAll('.question-container .question');
  const completed = Array.from(questionsDiv).filter(q => q.querySelector('.btn-complete').disabled || q.querySelector('.btn-complete').style.display === 'inline-block').length;
  const progressPercent = questionsDiv.length ? Math.round(completed / questionsDiv.length * 100) : 0;
  const bar = document.getElementById('progressBar');
  bar.style.width = progressPercent + '%';
  bar.textContent = progressPercent + '%';
}

// Download PDF with student answers
function downloadSession() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const questionsDiv = document.querySelectorAll('.question-container .question');
  let y = 10;
  questionsDiv.forEach((q, i) => {
    const questionText = q.querySelector('h3').textContent;
    const studentAnswer = q.querySelector('.student-input').value || 'Not answered';
    const correctAnswer = q.querySelector('.answer').textContent;
    doc.text(`${questionText}`, 10, y);
    y += 7;
    doc.text(`Your Answer: ${studentAnswer}`, 10, y);
    y += 7;
    doc.text(`Correct Answer: ${correctAnswer}`, 10, y);
    y += 10;
  });

  doc.save("tutoring_session.pdf");
}
</script>
</body>
</html>
