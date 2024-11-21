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
    header("Location: login.php");
    exit();
}

// Get the teacher ID from the session
$teacher_id = $_SESSION['user_id'];

// Get the POSTed data
$question_id = $_POST['question_id'];
$answer_text = $_POST['answer_text'];
$verification_status = 1; // Set verification status to 1 for teacher answers

// Check if there are already three verified answers for the question
$check_query = "SELECT COUNT(*) AS verified_count FROM (
                    SELECT answer_id FROM answer_teacher WHERE question_id = ? AND verification_status = 1
                    UNION ALL
                    SELECT answer_id FROM answer_student WHERE question_id = ? AND verification_status = 1
                ) AS all_verified_answers";

$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $question_id, $question_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$verified_count = $check_result->fetch_assoc()['verified_count'];
$check_stmt->close();

if ($verified_count >= 3) {
    echo "This question already has three verified answers.";
} else {
    // Prepare and bind the SQL query to insert the answer
    $stmt = $conn->prepare("INSERT INTO answer_teacher (question_id, answer_text, verification_status, teacher_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $question_id, $answer_text, $verification_status, $teacher_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Answer submitted successfully!";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
