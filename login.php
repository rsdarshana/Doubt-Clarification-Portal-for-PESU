<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" type="text/css" href="login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body background="image.jpg" class="body_deg">
    <center>
        <div class="form_deg">
            <center class="title_deg">
                <h1>Login Form</h1>
                <h4 style="color: red;">
                    <?php
                    // Display the login message if it exists
                    if (isset($_SESSION['loginMessage'])) {
                        echo $_SESSION['loginMessage'];
                        unset($_SESSION['loginMessage']); // Clear message after displaying
                    }
                    ?>
                </h4>
            </center>
            <form action="login_check.php" method="POST" class="login_form">
                <div>
                    <label class="label_deg">Username</label>
                    <input type="text" name="username" required>
                </div>

                <div>
                    <label class="label_deg">Password</label>
                    <input type="password" name="password" required>
                </div>

                <div>
                    <input class="btn btn-primary" type="submit" name="submit" value="Login">
                </div>
            </form>
        </div>
    </center>
</body>
</html>
