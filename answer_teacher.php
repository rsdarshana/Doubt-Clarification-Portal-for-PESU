<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "doubts";
$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

// Check if the teacher is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'teacher') {
    // Redirect to login page if the teacher is not logged in
    header("Location: login.php");
    exit();
}

// Fetch all questions
$query = "SELECT * FROM question";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer Questions (Teacher)</title>
    <link rel="stylesheet" type="text/css" href="answer.css"> <!-- External CSS file for styling -->
</head>
<body>
    <header class="header">
        <a href="adminhome.php" class="dashboard-title">Admin Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>
    <h1>Answer Questions</h1>

    <?php
    while ($question = mysqli_fetch_assoc($result)) {
        $question_id = $question['question_id'];
        $question_text = $question['question_text'];
        $question_course = $question['question_course'];
        $question_sem = $question['question_semester'];
    ?>
    <div class="question-box">
        <div class="tags">
            <span class="tag">Course: <?php echo htmlspecialchars($question_course); ?></span>
            <span class="tag">Semester: <?php echo htmlspecialchars($question_sem); ?></span>
        </div>
        <p><?php echo htmlspecialchars($question_text); ?></p>
        
        <button id="postAnswerBtn-<?php echo $question_id; ?>" onclick="showAnswerBox(<?php echo $question_id; ?>)">Post Answer</button>

        <div id="answerBox-<?php echo $question_id; ?>" class="answer-box" style="display: none;">
            <textarea id="answerText-<?php echo $question_id; ?>" rows="4" cols="50" placeholder="Type your answer here..."></textarea>
            <button onclick="submitAnswer(<?php echo $question_id; ?>)">Submit Answer</button>
            <p id="answerMessage-<?php echo $question_id; ?>" class="message"></p>
        </div>
    </div>
    <?php } ?>

    <script>
        let lastOpenBoxId = null;

        function showAnswerBox(questionId) {
            if (lastOpenBoxId !== null && lastOpenBoxId !== questionId) {
                document.getElementById('answerBox-' + lastOpenBoxId).style.display = 'none';
                document.getElementById('answerText-' + lastOpenBoxId).value = "";
                document.getElementById('postAnswerBtn-' + lastOpenBoxId).style.display = 'inline-block';
            }
            document.getElementById('postAnswerBtn-' + questionId).style.display = 'none';
            document.getElementById('answerBox-' + questionId).style.display = 'block';
            lastOpenBoxId = questionId;
        }

        function submitAnswer(questionId) {
            var answerTextElement = document.getElementById('answerText-' + questionId);
            var answerText = answerTextElement.value;
            var answerMessage = document.getElementById('answerMessage-' + questionId);

            if (answerText.trim() === "") {
                answerMessage.innerText = "Please type your answer.";
                return;
            }

            if (confirm("Are you sure you want to submit? You cannot change your response after submitting.")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "submit_answer_teacher.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        answerMessage.innerText = xhr.responseText;
                        answerTextElement.value = "";
                        setTimeout(function () {
                            answerMessage.innerText = "";
                        }, 7000);
                    }
                };
                xhr.send("question_id=" + questionId + "&answer_text=" + encodeURIComponent(answerText));
            }
        }
    </script>
</body>
</html>
