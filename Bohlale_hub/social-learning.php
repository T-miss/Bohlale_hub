<?php include("db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Social Learning Walls</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
  body { font-family: 'Inter', sans-serif; background: #f9fafb; margin: 0; }
  main { padding: 2rem; }
  .features h2 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .features p { margin-bottom: 1rem; color: #555; }

  .wall-posts { display: flex; flex-direction: column; gap: 1rem; }
  .post { background: #fff; padding: 1rem; border-radius: 12px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); position: relative; }
  .post h4 { margin: 0 0 0.5rem 0; color: #007bff; }
  .post p { margin: 0; }
  .like-btn { position: absolute; top: 1rem; right: 1rem; background: #ff4757; color: #fff; border: none; border-radius: 6px; padding: 0.2rem 0.5rem; cursor: pointer; font-size: 0.8rem; }
  .like-btn.liked { background: #2ed573; }

  .new-post { margin-top: 2rem; display: flex; flex-direction: column; gap: 0.5rem; }
  .new-post input, .new-post textarea { padding: 0.5rem; border-radius: 8px; border: 1px solid #ccc; width: 100%; font-size: 1rem; }
  .new-post button { align-self: flex-start; background: #1e90ff; color: #fff; padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; }
  .new-post button:hover { background: #0f7ae5; }

  .comments { margin-top: 1rem; padding-left: 1rem; border-left: 2px solid #eee; }
  .comment { margin-bottom: 0.5rem; font-size: 0.9rem; }
  .new-comment { margin-top: 0.5rem; display: flex; gap: 0.5rem; }
  .new-comment input { flex: 1; padding: 0.3rem; border-radius: 6px; border: 1px solid #ccc; }
  .new-comment button { background: #28a745; color: #fff; border: none; padding: 0.3rem 0.6rem; border-radius: 6px; cursor: pointer; font-size: 0.8rem; }
  .new-comment button:hover { background: #218838; }
</style>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><div class="logo-mark"></div><span class="brand-title">Student Portal</span></div>
    <nav class="main-nav">
      <a href="index.php" class="nav-item">Home</a>
      <button class="burger" id="burgerBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </nav>
    <div class="mobile-menu" id="mobileMenu"><a href="index.php">Home</a></div>
  </div>
</header>

<main>
<section class="features">
  <h2>Social Learning Walls 💬</h2>
  <p>Post questions, share insights, and collaborate with peers on interactive walls.</p>

  <div class="wall-posts" id="wallPosts">
    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    while($row = $result->fetch_assoc()): ?>
      <div class="post" data-id="<?php echo $row['id']; ?>">
        <h4><?php echo htmlspecialchars($row['username']); ?></h4>
        <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
        <button class="like-btn" onclick="likePost(<?php echo $row['id']; ?>, this)">Like (<?php echo $row['likes']; ?>)</button>

        <!-- Comments -->
        <div class="comments" id="comments-<?php echo $row['id']; ?>">
          <?php
          $pid = $row['id'];
          $cResult = $conn->query("SELECT * FROM comments WHERE post_id=$pid ORDER BY created_at ASC");
          while($c = $cResult->fetch_assoc()): ?>
            <div class="comment"><b><?php echo htmlspecialchars($c['username']); ?>:</b> <?php echo htmlspecialchars($c['comment']); ?></div>
          <?php endwhile; ?>
        </div>
        <div class="new-comment">
          <input type="text" placeholder="Your Name" id="cname-<?php echo $row['id']; ?>">
          <input type="text" placeholder="Write a comment..." id="cmsg-<?php echo $row['id']; ?>">
          <button onclick="addComment(<?php echo $row['id']; ?>)">Reply</button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <div class="new-post">
    <input type="text" id="username" placeholder="Your Name">
    <textarea id="message" rows="3" placeholder="Write your post here..."></textarea>
    <button onclick="addPost()">Post</button>
  </div>
</section>
</main>

<footer class="site-footer">
  <div class="container footer-bottom"><small>© <span id="year"></span> Student Portal</small></div>
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Mobile Menu
const burgerBtn = document.getElementById('burgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
burgerBtn.addEventListener('click', () => {
  const isExpanded = burgerBtn.getAttribute('aria-expanded') === 'true';
  burgerBtn.setAttribute('aria-expanded', !isExpanded);
  mobileMenu.classList.toggle('is-open');
});

// Add New Post
function addPost() {
  const name = document.getElementById('username').value.trim();
  const message = document.getElementById('message').value.trim();
  if(!name || !message) {
    alert("Please enter both name and message.");
    return;
  }

  fetch("post.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `username=${encodeURIComponent(name)}&message=${encodeURIComponent(message)}`
  })
  .then(res => res.text())
  .then(html => {
    document.getElementById('wallPosts').insertAdjacentHTML('afterbegin', html);
    document.getElementById('username').value = "";
    document.getElementById('message').value = "";
  });
}

// Like Post
function likePost(id, btn) {
  fetch("like.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `id=${id}`
  })
  .then(res => res.text())
  .then(count => {
    btn.textContent = "Liked (" + count + ")";
    btn.classList.add("liked");
  });
}

// Add Comment
function addComment(postId){
  const name = document.getElementById("cname-"+postId).value.trim();
  const msg = document.getElementById("cmsg-"+postId).value.trim();
  if(!name || !msg) {
    alert("Please enter name and comment.");
    return;
  }

  fetch("comment.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `post_id=${postId}&username=${encodeURIComponent(name)}&comment=${encodeURIComponent(msg)}`
  })
  .then(res => res.text())
  .then(html => {
    document.getElementById("comments-"+postId).insertAdjacentHTML("beforeend", html);
    document.getElementById("cname-"+postId).value = "";
    document.getElementById("cmsg-"+postId).value = "";
  });
}
</script>
</body>
</html>
