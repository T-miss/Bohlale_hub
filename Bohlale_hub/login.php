<?php
session_start();
$conn = new mysqli("localhost","root","","bohlale_hub");
if($conn->connect_error) die("DB Error: ".$conn->connect_error);

$error = "";
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");
    if($res->num_rows==1){
        $user = $res->fetch_assoc();
        if(password_verify($password,$user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: home.php");
            exit();
        } else { $error="Invalid email or password."; }
    } else { $error="Invalid email or password."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Student Portal — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
.auth-overlay { position: fixed; inset:0; background: rgba(0,0,0,0.6); display:flex; justify-content:center; align-items:center; z-index:100; }
.auth-container { background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); padding:32px 26px; border-radius:22px; width:360px; max-width:90%; color:#fff; text-align:center; box-shadow:0 16px 36px rgba(0,0,0,0.25); }
.auth-title { font-size:28px; font-weight:800; margin-bottom:8px; }
.auth-subtitle { font-size:14px; opacity:0.9; margin-bottom:22px; }
.auth-form { display:flex; flex-direction:column; gap:16px; }
.auth-form input { padding:12px; border-radius:12px; border:none; outline:none; font-size:15px; }
.auth-form button { background: linear-gradient(135deg,#6C63FF,#FF6584); border:none; color:#fff; padding:12px; border-radius:999px; font-weight:700; cursor:pointer; font-size:15px; transition:transform 0.15s; }
.auth-form button:hover { transform:scale(1.05); }
.auth-switch { font-size:13px; margin-top:10px; }
.auth-switch a { color:#fff; font-weight:700; text-decoration:none; }
.auth-switch a:hover { text-decoration:underline; }
.error-message { color:#ff6b6b; margin-bottom:10px; font-size:14px; }
</style>
</head>
<body>

<?php include('home-content.php'); ?>

<div class="auth-overlay">
  <div class="auth-container">
    <div class="auth-title">Login</div>
    <div class="auth-subtitle">Access your student account</div>
    <?php if($error!=""){ echo "<div class='error-message'>$error</div>"; } ?>
    <form class="auth-form" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <div class="auth-switch">Don't have an account? <a href="register.php">Register</a></div>
  </div>
</div>

</body>
</html>
