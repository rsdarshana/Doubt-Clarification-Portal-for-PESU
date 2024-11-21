<?php
session_start();
if(!isset($_SESSION['username']))
{
    header("location:login.php");
}

elseif($_SESSION['usertype']=='admin')
{
    header("location:login.php");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <link rel="stylesheet" type="text/css" href="admin.css">

</head>
<body>
    
    <header class="header">
        <a href="#" class="dashboard-title">Student Dashboard</a>

        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>
    <main class="content">
        <h1>Achieve Your Goals Quickly!</h1>
        <h2>Choose an Option:</h2>
        <div class="button-group">
            <a href="ask.php" class="btn btn-secondary">Ask Doubt</a>
            <a href="answer_student.php" class="btn btn-secondary">Answer Doubts</a>
            <a href="questions_with_answers.php" class="btn btn-secondary">View QA</a>
            <a href="leaderboard.php" class="btn btn-secondary">Leaderboard</a>
        </div>
    </main>

</body>
</html>