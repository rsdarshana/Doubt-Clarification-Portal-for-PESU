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

// Fetch questions with answers from answer_student table
$query = "SELECT q.question_id, q.question_text, a.answer_id, a.answer_text, a.verification_status
          FROM question q
          INNER JOIN answer_student a ON q.question_id = a.question_id
          ORDER BY q.question_id";
$result = mysqli_query($conn, $query);

$questions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $questions[$row['question_id']]['question_text'] = $row['question_text'];
    $questions[$row['question_id']]['answers'][] = [
        'answer_id' => $row['answer_id'],
        'answer_text' => $row['answer_text'],
        'verification_status' => $row['verification_status']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Answers</title>
    <link rel="stylesheet" type="text/css" href="verify.css">
    <script type="text/javascript">
        function confirmSubmission() {
            return confirm("Are you sure you want to proceed with submitting? Submitting deletes unverified answers.");
        }
    </script>
</head>
<body>
    <header class="header">
        <a href="adminhome.php" class="dashboard-title">Admin Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>
    <h1>Verify Answers</h1>

    <!-- Display success message if set -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']); // Clear the message after displaying it
            ?>
        </div>
    <?php endif; ?>

    <form id="verifyForm" method="POST" action="verify_submit.php" onsubmit="return confirmSubmission();">
        <?php foreach ($questions as $question_id => $question): ?>
            <div class="question-box">
                <p><strong>Question:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>

                <?php foreach ($question['answers'] as $answer): ?>
                    <div class="answer-box">
                        <p><?php echo htmlspecialchars($answer['answer_text']); ?></p>
                        <label class="verify-checkbox">
                            <input type="checkbox" name="verification_status[<?php echo $answer['answer_id']; ?>]" 
                                   value="1" <?php if ($answer['verification_status'] == 1) echo 'checked'; ?>>
                            Verify
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="submit-btn">Submit</button>
    </form>
</body>
</html>

<?php mysqli_close($conn); ?>
