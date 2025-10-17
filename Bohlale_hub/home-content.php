<!-- Header -->
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <div class="logo-mark" aria-hidden="true"></div>
      <span class="brand-title">Student Portal</span>
    </div>

    <nav class="main-nav" aria-label="Primary navigation">
      <a href="dashboard.php">Dashboard</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
    </nav>

    <button class="burger" id="burgerBtn" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- Mobile menu -->
  <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <a href="#features">Features</a>
    <a href="#about">About</a>
    <a href="#contact">Contact</a>
    <a href="#follow">Follow</a>
  </div>
</header>

<!-- HERO / Landing -->
<section class="hero">
  <div class="hero-layers" aria-hidden="true">
    <div class="layer layer-1"></div>
    <div class="layer layer-2"></div>
    <div class="layer layer-3"></div>
    <div class="layer layer-4"></div>
  </div>

  <div class="container hero-inner">
    <div class="hero-content">
      <p class="eyebrow">Welcome</p>
      <h1 class="hero-title">Student Portal — Learn, Connect, Thrive</h1>
      <p class="hero-copy">
        Access study paths, community events and wellbeing resources — all designed for students.
        Join study groups, track your progress and discover career tools in one place.
      </p>
      <div class="hero-actions">
        <a href="#features" class="btn btn-primary">Explore Features</a>
        <a href="#about" class="btn btn-ghost">Learn More</a>
      </div>
      <p class="hero-details">
        Discover personalized learning pathways, campus events, and wellbeing support built for busy students.
      </p>
    </div>

    <aside class="hero-panel" aria-hidden="false">
      <div class="student-illustration" role="img" aria-label="Student illustration"></div>
      <div class="greeting">
        <div id="greetingText" class="greeting-text">Good Morning 🌅</div>
        <div id="greetingTime" class="greeting-time">--/--/---- --:-- --</div>
      </div>
    </aside>
  </div>
</section>

<!-- Features -->
<section id="features" class="quick-features">
  <div class="container">
    <ul class="features-grid" aria-hidden="false">
      <li><strong>Personalized Paths</strong><p>Create study journeys tailored to your goals.</p></li>
      <li><strong>Peer Groups</strong><p>Join classmates and study together or share notes.</p></li>
      <li><strong>Events & Workshops</strong><p>Discover campus talks, hackathons and wellbeing sessions.</p></li>
    </ul>
  </div>
</section>

<!-- Footer -->
<footer class="site-footer" id="follow">
  <div class="container footer-inner">
    <div class="follow horizontal">
      <div class="follow-left">
        <h4>Follow Us</h4>
        <small class="follow-sub">Stay updated on socials</small>
      </div>
      <div class="follow-right">
        <a href="https://www.instagram.com" target="_blank" rel="noopener" aria-label="Instagram">
          <img src="https://img.icons8.com/color/48/000000/instagram-new.png" alt="Instagram">
        </a>
        <a href="https://www.facebook.com" target="_blank" rel="noopener" aria-label="Facebook">
          <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Facebook">
        </a>
      </div>
    </div>

    <div class="footer-news">
      <h4>Trending</h4>
      <div class="ticker" aria-hidden="false">
        <div class="ticker-track" id="tickerTrack">
          <span>New AI study tool released •</span>
          <span>Campus career fair next week •</span>
          <span>Mental wellbeing webinar — register now •</span>
        </div>
      </div>
    </div>
  </div>

  <div class="container footer-bottom">
    <small>© <span id="year"></span> Student Portal — layered landing inspired design.</small>
  </div>
</footer>

<!-- Scripts -->
<script>
const burgerBtn = document.getElementById('burgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
burgerBtn.addEventListener('click', ()=>{
  const expanded = burgerBtn.getAttribute('aria-expanded')==='true';
  burgerBtn.setAttribute('aria-expanded', String(!expanded));
  mobileMenu.style.display = expanded ? 'none':'block';
  mobileMenu.setAttribute('aria-hidden', String(expanded));
});

function updateGreetingAndTime(){
  const gText = document.getElementById('greetingText');
  const gTime = document.getElementById('greetingTime');
  const now = new Date();
  const hour = now.getHours();
  let greeting = 'Good day 👋';
  if(hour<12) greeting='Good Morning 🌅';
  else if(hour<18) greeting='Good Afternoon ☀️';
  else greeting='Good Evening 🌙';
  gText.textContent = greeting;

  const dd = String(now.getDate()).padStart(2,'0');
  const mm = String(now.getMonth()+1).padStart(2,'0');
  const yyyy = now.getFullYear();
  let hours12 = now.getHours()%12||12;
  const mins = String(now.getMinutes()).padStart(2,'0');
  const ampm = now.getHours()>=12?'PM':'AM';
  gTime.textContent = `${dd}/${mm}/${yyyy} ${hours12}:${mins} ${ampm}`;
}
updateGreetingAndTime();
setInterval(updateGreetingAndTime,1000);

document.getElementById('year').textContent = new Date().getFullYear();
const tickerTrack = document.getElementById('tickerTrack');
tickerTrack.innerHTML = tickerTrack.innerHTML + tickerTrack.innerHTML;
</script>
