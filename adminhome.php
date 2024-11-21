<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
} elseif ($_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
    <header class="header">
        <a href="#" class="dashboard-title">Admin Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>
    <main class="content">
        <h1>Make Doubt Solving Convenient And Fast!</h1>
        <h2>Choose an Option:</h2>
        <div class="button-group">
            <a href="verify.php" class="btn btn-secondary">Verify Answer</a>
            <a href="answer_teacher.php" class="btn btn-secondary">Answer Doubts</a>
        </div>
    </main>
</body>
</html>
