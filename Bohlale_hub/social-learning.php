<?php include("db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Social Learning Wall</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#E8DFF0;
  --accent1:#ff2b7d;
  --accent2:#b14b7f;
  --accent3:#ffd6e8;
  --deep:#2b0028;
  --glass: rgba(255,255,255,0.14);
  --container-width:1100px;
  --radius:20px;
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
}

/* Body & Layout */
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  margin: 0;
  background: linear-gradient(180deg,var(--bg),#f7eef8 70%);
  color: var(--deep);
  transition: 0.3s;
}
.container {
  width:min(94%,var(--container-width));
  margin: 0 auto;
  padding: 2rem 0;
  flex: 1; /* pushes footer down */
}

/* Header */
header{
  position:sticky; top:0;
  background:rgba(255,255,255,0.2);
  padding:1rem 2rem;
  display:flex;
  justify-content:space-between;
  align-items:center;
  border-bottom:1px solid rgba(0,0,0,0.05);
  backdrop-filter:blur(12px);
  z-index:100;
}
header h1{
  margin:0;
  color:var(--accent1);
  font-size:2rem;
  letter-spacing:1px;
}
header a{
  text-decoration:none;
  color:var(--accent2);
  font-weight:500;
  transition:0.3s;
}
header a:hover{color:var(--accent1);}

/* Wall Posts */
.wall-posts{display:flex; flex-direction:column; gap:1rem;}
.post{
  background:linear-gradient(145deg,#ffffff,#ffe6f0);
  padding:1rem 1.5rem; border-radius:var(--radius);
  box-shadow:0 5px 15px rgba(0,0,0,0.1);
  position:relative;
  transition:0.3s;
  overflow:hidden;
}
.post:hover{
  transform:translateY(-5px);
  box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.post h4{
  margin:0 0 0.5rem 0;
  color:var(--accent1);
  display:flex;
  align-items:center;
  gap:0.5rem;
  font-size:1.1rem;
}
.post h4 .avatar{
  width:32px; height:32px; border-radius:50%;
  background:white;
  border:2px solid var(--accent2);
}

/* Reactions */
.like-btn{
  position:absolute; top:1rem; right:1rem; 
  background:var(--accent1); color:white; 
  border:none; border-radius:12px; padding:0.3rem 0.7rem; 
  cursor:pointer; font-size:0.85rem;
  transition:0.3s;
}
.like-btn:hover{transform:scale(1.1);}
.like-btn.liked{background:var(--accent2);}
.reactions{
  margin-top:0.8rem;
  display:flex; gap:0.5rem;
}
.reactions span{
  cursor:pointer;
  font-size:1.2rem;
  transition:0.2s;
}
.reactions span:hover{transform:scale(1.3);}

/* Comments */
.comments{
  margin-top:1rem; padding-left:1rem; border-left:2px dashed #ffc0dc;
  max-height:0; overflow:hidden; transition:max-height 0.4s ease;
}
.comments.active{max-height:500px;}
.comment{
  margin-bottom:0.5rem;
  font-size:0.9rem;
  display:flex;
  align-items:center;
  gap:0.5rem;
}
.comment .comment-avatar{
  width:24px; height:24px; border-radius:50%;
  background:white;
  border:1px solid var(--accent2);
}

/* New Post / Comment */
.new-post, .new-comment{
  margin-top:1rem;
  display:flex; flex-direction:column; gap:0.5rem;
}
.new-post input, .new-post textarea, .new-comment input{
  padding:0.5rem; border-radius:12px; border:1px solid #ccc; width:100%;
  font-size:0.95rem;
}
.new-post button, .new-comment button{
  background:var(--accent1); color:white; border:none; padding:0.5rem 1rem;
  border-radius:12px; cursor:pointer; transition:0.3s;
}
.new-post button:hover, .new-comment button:hover{background:var(--accent2);}

/* Toggle comments button */
.toggle-comments{
  margin-top:0.5rem;
  background:var(--accent3);
  border:none;
  padding:0.3rem 0.6rem;
  border-radius:12px;
  cursor:pointer;
  font-size:0.85rem;
  transition:0.2s;
}
.toggle-comments:hover{background:var(--accent1); color:white;}

/* Footer */
footer{
  background:var(--accent2);
  color:white;
  text-align:center;
  padding:1rem;
  border-radius:var(--radius);
  margin-top:auto;
}
</style>
</head>
<body>

<header>
  <h1>📚 Social Learning Wall</h1>
  <a href="dashboard.php"> Home</a>
</header>

<div class="container">

<!-- Wall Posts -->
<div class="wall-posts" id="wallPosts">
<?php
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
while($row = $result->fetch_assoc()): ?>
  <div class="post" data-id="<?php echo $row['id']; ?>">
    <h4><span class="avatar"></span> <?php echo htmlspecialchars($row['username']); ?></h4>
    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
    <div class="reactions">
      <span onclick="reactEmoji(<?php echo $row['id']; ?>,'👍')">👍</span>
      <span onclick="reactEmoji(<?php echo $row['id']; ?>,'💡')">💡</span>
      <span onclick="reactEmoji(<?php echo $row['id']; ?>,'❤️')">❤️</span>
    </div>
    <button class="like-btn" onclick="likePost(<?php echo $row['id']; ?>, this)">Like (<?php echo $row['likes']; ?>)</button>
    <button class="toggle-comments" onclick="toggleComments(<?php echo $row['id']; ?>)">Show Comments</button>

    <div class="comments" id="comments-<?php echo $row['id']; ?>">
      <?php
      $pid = $row['id'];
      $cResult = $conn->query("SELECT * FROM comments WHERE post_id=$pid ORDER BY created_at ASC");
      while($c = $cResult->fetch_assoc()): ?>
        <div class="comment"><span class="comment-avatar"></span><b><?php echo htmlspecialchars($c['username']); ?>:</b> <?php echo htmlspecialchars($c['comment']); ?></div>
      <?php endwhile; ?>
      <div class="new-comment">
        <input type="text" placeholder="Your Name" id="cname-<?php echo $row['id']; ?>">
        <input type="text" placeholder="Write a comment..." id="cmsg-<?php echo $row['id']; ?>">
        <button onclick="addComment(<?php echo $row['id']; ?>)">Reply</button>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>

<!-- Add Post -->
<div class="new-post">
  <input type="text" id="username" placeholder="Your Name">
  <textarea id="message" rows="3" placeholder="Share your learning or question..."></textarea>
  <button onclick="addPost()">Post</button>
</div>

</div>

<footer>
  © <span id="year"></span> Student Portal
</footer>

<script>
document.getElementById("year").textContent = new Date().getFullYear();

// Toggle comments
function toggleComments(postId){
  const c = document.getElementById("comments-"+postId);
  c.classList.toggle("active");
}

// Add Post
function addPost(){
  const name=document.getElementById("username").value.trim();
  const msg=document.getElementById("message").value.trim();
  if(!name||!msg) return alert("Enter name & message");
  fetch("post.php",{method:"POST", headers:{"Content-Type":"application/x-www-form-urlencoded"}, body:`username=${encodeURIComponent(name)}&message=${encodeURIComponent(msg)}`})
  .then(res=>res.text()).then(html=>{
    document.getElementById("wallPosts").insertAdjacentHTML("afterbegin",html);
    document.getElementById("username").value="";
    document.getElementById("message").value="";
  });
}

// Like Post
function likePost(id,btn){
  fetch("like.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:`id=${id}`})
  .then(res=>res.text()).then(count=>{
    btn.textContent="Liked ("+count+")";
    btn.classList.add("liked");
  });
}

// Add Comment
function addComment(postId){
  const name=document.getElementById("cname-"+postId).value.trim();
  const msg=document.getElementById("cmsg-"+postId).value.trim();
  if(!name||!msg) return alert("Enter name & comment");
  fetch("comment.php",{method:"POST", headers:{"Content-Type":"application/x-www-form-urlencoded"}, body:`post_id=${postId}&username=${encodeURIComponent(name)}&comment=${encodeURIComponent(msg)}`})
  .then(res=>res.text()).then(html=>{
    document.getElementById("comments-"+postId).insertAdjacentHTML("beforeend",html);
    document.getElementById("cname-"+postId).value="";
    document.getElementById("cmsg-"+postId).value="";
  });
}

// Emoji reaction
function reactEmoji(postId,emoji){
  const c = document.getElementById("comments-"+postId);
  const reaction = document.createElement("div");
  reaction.className="comment";
  reaction.innerHTML=`<span>${emoji}</span>`;
  c.appendChild(reaction);
}
</script>
</body>
</html>
