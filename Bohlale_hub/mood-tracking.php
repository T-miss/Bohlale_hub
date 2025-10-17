<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "bohlale_hub";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Mood documents and advice mapping
$moodDocs = [
    '😄' => 'docs/happy_guide.pdf',
    '😐' => 'docs/neutral_guide.pdf',
    '😔' => 'docs/sad_guide.pdf',
    '😡' => 'docs/angry_guide.pdf'
];

$moodAdviceMap = [
    '😄' => 'You seem happy! Keep maintaining your positive habits and share your happiness with others!',
    '😐' => 'Feeling neutral is okay. Engage in activities you enjoy to boost your mood.',
    '😔' => 'Feeling sad? Try walking, music, or talking to friends. Seek professional help if it persists.',
    '😡' => 'Feeling angry? Practice deep breathing or mindfulness. Professional guidance can help if frequent.'
];

// Handle mood submission
$uploadMsg = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_mood'])){
    $mood = isset($_POST['mood']) ? $conn->real_escape_string($_POST['mood']) : '';
    $advice = isset($moodAdviceMap[$mood]) ? $conn->real_escape_string($moodAdviceMap[$mood]) : '';
    $date = date('Y-m-d');

    if($mood === ''){
        $uploadMsg = "Please select a mood before submitting.";
    } else {
        $sql = "INSERT INTO mood_tracker (mood, entry_date, note) VALUES ('$mood', '$date', '$advice')";
        if($conn->query($sql)){
            $uploadMsg = "Mood logged successfully!";
        } else {
            $uploadMsg = "Error logging mood: " . $conn->error;
        }
    }
}

// Fetch past moods
$sql = "SELECT * FROM mood_tracker ORDER BY entry_date DESC";
$result = $conn->query($sql);
$moodData = [];
if($result){
    while($row = $result->fetch_assoc()){
        $moodData[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mood Tracking</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
  body { font-family: 'Inter', sans-serif; background: #f0f4f8; margin: 0; }
  main { padding: 2rem; }
  .features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .features p { margin-bottom: 1rem; color: #555; }

  /* Mood Buttons */
  .mood-buttons { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
  .mood-buttons button { font-size: 1.5rem; padding: 0.5rem 1rem; border-radius: 12px; border: none; cursor: pointer; transition: 0.2s; }
  .mood-buttons button:hover { transform: scale(1.1); }

  /* Mood History */
  .mood-history { display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 0.5rem; margin-top: 1rem; }
  .mood-day { background: #fff; padding: 1rem; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; transition: transform 0.2s; cursor: pointer; }
  .mood-day:hover { transform: translateY(-3px); }

  /* Mood Trend Chart */
  .trend-chart { margin-top: 2rem; background: #fff; padding: 1rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  .trend-chart h3 { margin-top: 0; color: #333; }
  .trend-bar { height: 15px; background: #007bff; border-radius: 8px; margin: 0.3rem 0; transition: width 0.5s; color: #fff; text-align: right; padding-right: 5px; font-size: 0.8rem; }

  /* Mood Advice */
  .mood-advice { margin-top: 1rem; background: #fff; padding: 1rem; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
  .mood-advice h4 { margin-top: 0; }
  .mood-advice p { margin: 0.5rem 0; }
  .mood-advice a { color: #007bff; text-decoration: none; }
  .mood-advice a:hover { text-decoration: underline; }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Mood Tracking 😊</h2>
  <p>Log your daily mood and monitor mental well-being trends over time.</p>

  <?php if($uploadMsg) echo "<div style='margin-bottom:1rem;color:green;'>$uploadMsg</div>"; ?>

  <!-- Mood Buttons Form -->
  <form method="post" id="moodForm">
    <input type="hidden" name="mood" id="selectedMood">
    <input type="hidden" name="log_mood" value="1">
    <input type="hidden" name="advice" id="moodAdviceInput">
    <div class="mood-buttons">
      <button type="button" value="😄">😄 Happy</button>
      <button type="button" value="😐">😐 Neutral</button>
      <button type="button" value="😔">😔 Sad</button>
      <button type="button" value="😡">😡 Angry</button>
    </div>
  </form>

  <!-- Mood History -->
  <h3>Mood History</h3>
  <div class="mood-history" id="moodHistory">
    <?php foreach(array_reverse($moodData) as $entry):
        $doc = isset($moodDocs[$entry['mood']]) ? $moodDocs[$entry['mood']] : '';
    ?>
      <div class="mood-day" title="<?= htmlspecialchars($entry['note']) ?>">
        <strong><?= htmlspecialchars($entry['entry_date']) ?></strong><br>
        <?= htmlspecialchars($entry['mood']) ?><br>
        <?php if($doc): ?>
          <a href="<?= $doc ?>" download style="font-size:0.8rem;">Download guide</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Mood Trend -->
  <div class="trend-chart">
    <h3>Mood Trend</h3>
    <div id="trendBars">
      <?php
        $counts = ['😄'=>0,'😐'=>0,'😔'=>0,'😡'=>0];
        foreach($moodData as $entry){
          if(isset($counts[$entry['mood']])) $counts[$entry['mood']]++;
        }
        foreach($counts as $m=>$c):
          $width = $c*20;
          echo "<div class='trend-bar' style='width:{$width}px'>{$m} {$c}</div>";
        endforeach;
      ?>
    </div>
  </div>

  <!-- Mood Advice -->
  <div class="mood-advice" id="moodAdvice">
    <h4>Advice & Resources</h4>
    <p>Select a mood above to see advice and download resources for improving your well-being.</p>
  </div>

</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Mood advice messages and document links
const moodAdvice = {
  '😄': {
    text: 'You seem happy! Keep maintaining your positive habits and share your happiness with others!',
    doc: 'docs/happy_guide.pdf'
  },
  '😐': {
    text: 'Feeling neutral is okay. Engage in activities you enjoy to boost your mood.',
    doc: 'docs/neutral_guide.pdf'
  },
  '😔': {
    text: 'Feeling sad? Try taking a short walk, listening to music, or talking to a friend. Seek professional help if it persists.',
    doc: 'docs/sad_guide.pdf'
  },
  '😡': {
    text: 'Feeling angry? Practice deep breathing, mindfulness, or take a short break. Professional guidance can help if frequent.',
    doc: 'docs/angry_guide.pdf'
  }
};

// Handle mood button clicks
document.querySelectorAll('.mood-buttons button').forEach(btn=>{
  btn.addEventListener('click', e=>{
    const mood = btn.value;
    document.getElementById('selectedMood').value = mood;
    document.getElementById('moodAdviceInput').value = moodAdvice[mood].text;

    // Show advice immediately
    const adviceEl = document.getElementById('moodAdvice');
    adviceEl.innerHTML = `<h4>Advice & Resources</h4>
      <p>${moodAdvice[mood].text}</p>
      <p>Download a guide: <a href="${moodAdvice[mood].doc}" download>Click here to download</a></p>
      <p>Read more: <a href="https://www.mentalhealth.org.uk/publications/guide-to-good-mental-health" target="_blank">Mental Health Guide</a></p>`;

    // Submit form to save in DB
    document.getElementById('moodForm').submit();
  });
});
</script>
</body>
</html>



