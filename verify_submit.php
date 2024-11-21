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

// Check if verification status data was submitted
if (isset($_POST['verification_status'])) {
    $verified_answers = $_POST['verification_status'];

    // Loop through each submitted answer
    foreach ($verified_answers as $answer_id => $status) {
        $verification_status = ($status == 1) ? 1 : 0;

        // Update the verification status for verified answers in answer_student
        if ($verification_status == 1) {
            $stmt = $conn->prepare("UPDATE answer_student SET verification_status = ? WHERE answer_id = ?");
            $stmt->bind_param("ii", $verification_status, $answer_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Delete unverified answers from answer_student table
    $verified_answer_ids = implode(',', array_keys($verified_answers));
    $delete_query = "DELETE FROM answer_student WHERE answer_id NOT IN ($verified_answer_ids)";
    mysqli_query($conn, $delete_query);

    // Set a success message in the session
    $_SESSION['success_message'] = "Verification statuses updated successfully!";
} else {
    $_SESSION['success_message'] = "No verification statuses were updated.";
}

// Redirect back to verify.php to display the success message
header("Location: verify.php");
exit();

mysqli_close($conn);
?>
