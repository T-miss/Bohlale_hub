<?php
session_start();
$student_id = 1; // replace with $_SESSION['student_id']

$host="localhost"; $user="root"; $pass=""; $db="bohlale_hub";
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die(json_encode(['error'=>"DB connection failed"]));

$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : 'manual';

$res = $conn->query("SELECT * FROM student_levels WHERE student_id=$student_id");
$student = $res->fetch_assoc();
$current_xp = $student['total_xp'];
$daily_streak = $student['daily_streak'];
$last_daily_bonus = $student['last_daily_bonus'];
$today = date('Y-m-d');

// Daily streak check
$streak_bonus = 0;
if($type=='daily' && $last_daily_bonus!=$today){
    $streak_bonus = 50 + ($daily_streak*10); // bonus XP
    $daily_streak = ($last_daily_bonus == date('Y-m-d',strtotime('-1 day'))) ? $daily_streak+1 : 1;
    $conn->query("UPDATE student_levels SET daily_streak=$daily_streak, last_daily_bonus='$today' WHERE student_id=$student_id");
}

// Update XP
$total_gain = $amount + $streak_bonus;
if($total_gain>0){
    $conn->query("UPDATE student_levels SET total_xp=total_xp+$total_gain,last_activity=NOW() WHERE student_id=$student_id");

    // Fetch updated XP
    $res = $conn->query("SELECT total_xp,daily_streak FROM student_levels WHERE student_id=$student_id");
    $student = $res->fetch_assoc();
    $new_xp = $student['total_xp'];
    $daily_streak = $student['daily_streak'];

    // Determine level
    $level_name='Beginner';
    if($new_xp>=10000) $level_name='Master';
    elseif($new_xp>=5000) $level_name='Advanced Learner';
    elseif($new_xp>=1000) $level_name='Intermediate';
    $conn->query("UPDATE student_levels SET level_name='$level_name' WHERE student_id=$student_id");

    // Badge unlocks
    $badge_res = $conn->query("SELECT * FROM student_badges WHERE student_id=$student_id AND unlocked=0");
    while($badge = $badge_res->fetch_assoc()){
        $unlock=false;
        if($badge['badge_name']=='XP Achiever' && $new_xp>=1000) $unlock=true;
        if($badge['badge_name']=='Streak Master' && $daily_streak>=7) $unlock=true;
        if($unlock) $conn->query("UPDATE student_badges SET unlocked=1, date_unlocked=NOW() WHERE id=".$badge['id']);
    }

    echo json_encode(['success'=>true,'new_xp'=>$new_xp,'level_name'=>$level_name,'daily_streak'=>$daily_streak,'total_gain'=>$total_gain]);
}else{
    echo json_encode(['error'=>"Invalid XP amount"]);
}

