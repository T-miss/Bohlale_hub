<?php
session_start();

// Example: check if user is logged in
$isLoggedIn = isset($_SESSION['username']);
$siteTitle  = "Student Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $siteTitle; ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <!-- Your CSS -->
    <link rel="stylesheet" href="style-news.css" />

    <style>
      /* Inline tweaks */
      .feature-card {
        text-decoration: none;
        color: inherit;
        text-align: center; /* center feature titles and icons */
      }
      .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
      }
      .features-section {
        margin-top: 3rem;
      }
      .section-heading {
        text-align: center;
        font-weight: 700;
        margin-bottom: 1.5rem;
      }
    </style>
</head>
<body>

   <!-- Header --> 
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <img src="https://image2url.com/images/1760714493363-e0fbca59-5907-44cc-b416-2406a998e9f3.jpeg" 
           alt="Student Portal Logo" 
           class="logo-mark">
      
      <span class="brand-title">BOHLALE HUB</span>
    </div>
            <nav class="main-nav">
                <a href="#" class="search-link" id="openSearch" aria-label="Search">
                    <i class="fas fa-search"></i>
                </a>
                <a href="home.php">Home</a>
                <a href="dashboard.php"> Dashboard</a>
                <a href="#"> Engagement </a>
                <a href="#"> Learning </a>
                <a href="#">Career </a>
                <a href="#"> Well-being </a>
                <a href="#"> Community</a>

                <?php if ($isLoggedIn): ?>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>

                <button class="burger" id="burger" aria-label="Toggle menu">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>

        <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
            <a href="#">Settings</a>
            <a href="#"> About</a>
            <a href="#"> Contact </a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-inner">
            <h1 class="hero-title magic-text">
                Welcome <?php echo $isLoggedIn ? htmlspecialchars($_SESSION['username']) : "to Your Learning Portal"; ?>
            </h1>
            <p class="hero-copy">Explore features, track progress, connect with peers, and grow your skills!</p>
            <a href="#features" class="btn btn-primary">Explore Features</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-wrapper" id="features">

        <!-- Student Engagement and Motivation -->
        <div class="features-section">
            <h2 class="section-heading">Student Engagement and Motivation</h2>
            <div class="features-grid">
                <a href="gamified-progress.php" class="feature-card">
                    <i class="fas fa-trophy fa-2x"></i>
                    <h3>Gamified Progress Tracking</h3>
                    <p>Track your achievements and earn badges as you learn.</p>
                </a>
                <a href="social-learning.php" class="feature-card">
                    <i class="fas fa-users fa-2x"></i>
                    <h3>Social Learning Walls</h3>
                    <p>Interact with peers, share ideas, and collaborate on materials.</p>
                </a>
            </div>
        </div>

        <!-- Personalized Learning and Academic Support -->
        <div class="features-section">
            <h2 class="section-heading">Personalized Learning and Academic Support</h2>
            <div class="features-grid">
                <a href="ai-pathways.php" class="feature-card">
                    <i class="fas fa-book fa-2x"></i>
                    <h3>AI-powered Learning Pathways</h3>
                    <p>Create customized learning paths based on your style, pace, and goals.</p>
                </a>
                <a href="ai-tutoring.php" class="feature-card">
                    <i class="fas fa-robot fa-2x"></i>
                    <h3>Intelligent Tutoring Systems</h3>
                    <p>One-on-one AI tutoring to help you master concepts.</p>
                </a>
            </div>
        </div>

        <!-- Career Development and Preparation -->
        <div class="features-section">
            <h2 class="section-heading">Career Development and Preparation</h2>
            <div class="features-grid">
                <a href="career-roadmap.php" class="feature-card">
                    <i class="fas fa-map fa-2x"></i>
                    <h3>Career Roadmap Builder</h3>
                    <p>Create a personalized career roadmap with actionable steps.</p>
                </a>
                <a href="industry-insights.php" class="feature-card">
                    <i class="fas fa-chart-line fa-2x"></i>
                    <h3>Industry Insights and Trends</h3>
                    <p>Access latest industry research, trends, and skill requirements.</p>
                </a>
            </div>
        </div>

        <!-- Mental Health and Well-being -->
        <div class="features-section">
            <h2 class="section-heading">Mental Health and Well-being</h2>
            <div class="features-grid">
                <a href="mood-tracking.php" class="feature-card">
                    <i class="fas fa-smile fa-2x"></i>
                    <h3>Mood Tracking</h3>
                    <p>Monitor your emotions and access support resources.</p>
                </a>
                <a href="wellness-challenges.php" class="feature-card">
                    <i class="fas fa-heartbeat fa-2x"></i>
                    <h3>Wellness Challenges</h3>
                    <p>Participate in activities that promote mental health.</p>
                </a>
            </div>
        </div>

        <!-- Community Building and Social Connections -->
        <div class="features-section">
            <h2 class="section-heading">Community Building and Social Connections</h2>
            <div class="features-grid">
                <a href="networking.php" class="feature-card">
                    <i class="fas fa-handshake fa-2x"></i>
                    <h3>Student Networking Platform</h3>
                    <p>Connect with peers and mentors with similar interests.</p>
                </a>
                <a href="event-calendar.php" class="feature-card">
                    <i class="fas fa-calendar-alt fa-2x"></i>
                    <h3>Campus Event Calendar</h3>
                    <p>Stay updated with workshops, seminars, and campus events.</p>
                </a>
            </div>
        </div>

        <!-- Smart Timetable and Resources-->
        <div class="features-section">
            <h2 class="section-heading">Smart Timetable and Resources</h2>
            <div class="features-grid">
                <a href="smartTimetable.php" class="feature-card">
                    <i class="fas fa-tablet fa-2x"></i>
                    <h3>Smart Timetable</h3>
                    <p>Organize your classes, assignments, and study sessions effectively.</p>
                </a>
            
    
            <div class="features-grid">
                <a href="resources.php" class="feature-card">
                    <i class="fas fa-book fa-2x"></i>
                    <h3>Resources</h3>
                    <p>Access study guides, tools, and support material.</p>
                </a>
            </div>
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
                    <a href="https://www.instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
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
        <div class="footer-bottom">&copy; <?php echo date("Y"); ?> <?php echo $siteTitle; ?>. All rights reserved.</div>
    </footer>

    <!-- Search Modal -->
    <div class="modal" id="searchModal" aria-hidden="true" role="dialog" aria-labelledby="searchTitle">
      <div class="modal-content" role="document">
        <button class="modal-close-btn" id="closeSearch" aria-label="Close search">&times;</button>
        <h3 id="searchTitle">Search Student Portal</h3>

        <div class="search-input-container">
          <input type="text" id="searchInput" class="search-input" placeholder="Search features, pages, or topics…" />
          <button class="search-btn" id="runSearch"><i class="fas fa-search"></i></button>
        </div>

        <div id="searchResults" class="search-results" aria-live="polite"></div>

        <div class="search-suggestions">
          <h4>Try:</h4>
          <ul class="suggestions-list" id="suggestionsList">
            <li class="suggestion-item">AI Tutoring</li>
            <li class="suggestion-item">Event Calendar</li>
            <li class="suggestion-item">Career Roadmap</li>
            <li class="suggestion-item">Wellness Challenges</li>
            <li class="suggestion-item">Resources</li>
            <li class="suggestion-item">smartTimetable</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script>
        // Burger Menu Toggle
        const burger = document.getElementById('burger');
        const mobileMenu = document.getElementById('mobileMenu');
        burger.addEventListener('click', () => {
            mobileMenu.classList.toggle('is-open');
            const isOpen = mobileMenu.classList.contains('is-open');
            mobileMenu.setAttribute('aria-hidden', String(!isOpen));
        });

        // Clone ticker text for seamless loop
        const tickerTrack = document.getElementById('tickerTrack');
        if (tickerTrack) tickerTrack.innerHTML += tickerTrack.innerHTML;

        // --- Search Modal Logic ---
        const searchModal   = document.getElementById('searchModal');
        const openSearchBtn = document.getElementById('openSearch');
        const closeSearchBtn= document.getElementById('closeSearch');
        const searchInput   = document.getElementById('searchInput');
        const runSearchBtn  = document.getElementById('runSearch');
        const resultsBox    = document.getElementById('searchResults');
        const suggestions   = document.getElementById('suggestionsList');

        const pages = [
          { title: 'AI Tutoring', href: 'ai-tutoring.php', blurb: 'Personalized tutoring with AI assistance.' },
          { title: 'Gamified Progress', href: 'gamified-progress.php', blurb: 'Track achievements and earn badges.' },
          { title: 'Social Learning', href: 'social-learning.php', blurb: 'Collaborate and share with peers.' },
          { title: 'Career Roadmap', href: 'career-roadmap.php', blurb: 'Plan your career and learning path.' },
          { title: 'Mood Tracking', href: 'mood-tracking.php', blurb: 'Track emotions and wellbeing.' },
          { title: 'Event Calendar', href: 'event-calendar.php', blurb: 'Upcoming events and deadlines.' },
          { title: 'Networking', href: 'networking.php', blurb: 'Connect with students and pros.' },
          { title: 'Industry Insights', href: 'industry-insights.php', blurb: 'Trends and in-demand skills.' },
          { title: 'Wellness Challenges', href: 'wellness-challenges.php', blurb: 'Improve learning and wellbeing.' },
          { title: 'Resources', href: 'resources.php', blurb: 'Study guides, tools, and support.' },
          { title: 'smartTimetable', href: 'smartTimetable.php', blurb: 'Organize your study schedule effectively.' },
        ];

        function openSearch(e) {
          if (e) e.preventDefault();
          searchModal.classList.add('is-open');
          searchModal.setAttribute('aria-hidden', 'false');
          setTimeout(() => searchInput.focus(), 50);
        }

        function closeSearch() {
          searchModal.classList.remove('is-open');
          searchModal.setAttribute('aria-hidden', 'true');
          resultsBox.innerHTML = '';
          searchInput.value = '';
        }

        openSearchBtn.addEventListener('click', openSearch);
        closeSearchBtn.addEventListener('click', closeSearch);

        searchModal.addEventListener('click', (e) => {
          if (e.target === searchModal) closeSearch();
        });

        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && searchModal.classList.contains('is-open')) closeSearch();
        });

        function runSearch() {
          const q = searchInput.value.trim().toLowerCase();
          if (!q) {
            resultsBox.innerHTML = '<em>Please enter a search term.</em>';
            return;
          }
          const matches = pages.filter(p => p.title.toLowerCase().includes(q) || p.blurb.toLowerCase().includes(q));
          if (!matches.length) {
            resultsBox.innerHTML = `<em>No results for “${q}”. Try another term.</em>`;
            return;
          }
          resultsBox.innerHTML = matches.map(m => `
            <div class="result-item">
              <h5>${m.title}</h5>
              <div>${m.blurb}</div>
              <div style="margin-top:8px;"><a class="download-link" href="${m.href}">Open</a></div>
            </div>
          `).join('');
        }

        runSearchBtn.addEventListener('click', runSearch);
        searchInput.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') runSearch();
        });

        suggestions.addEventListener('click', (e) => {
          const chip = e.target.closest('.suggestion-item');
          if (!chip) return;
          searchInput.value = chip.textContent.trim();
          runSearch();
        });
    </script>
</body>
</html>
