<?php
session_start();
include 'db_connection.php';

$message = "";

// Check if the student is logged in and get the student_id
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'student') {
    // Redirect to login page if the student is not logged in
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id']; // Fetch the student_id from the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST['question_text'];
    $question_course = $_POST['question_course'];
    $question_semester = $_POST['question_semester'];

    // Check if the student_id exists in the student table to prevent foreign key issues
    $checkStudentQuery = "SELECT student_id FROM student WHERE student_id = ?";
    $checkStmt = $conn->prepare($checkStudentQuery);
    $checkStmt->bind_param("i", $student_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        // If student_id does not exist, display an error message and stop execution
        $message = "Student ID not found. Please log in with a valid account.";
        header("Location: ask.php?message=" . urlencode($message));
        exit();
    }

    // Handle file upload if an image was uploaded
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_path = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $upload_dir = 'uploads/';

        // Create the uploads directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Define the full path to save the image
        $image_path = $upload_dir . uniqid() . '_' . $image_name;

        // Move the uploaded file to the specified directory
        if (!move_uploaded_file($image_tmp_path, $image_path)) {
            $message = "Error uploading image.";
            header("Location: ask.php?message=" . urlencode($message));
            exit();
        }
    }

    // Prepare the SQL statement to insert the question into the question table with the image path
    $query = "INSERT INTO question (student_id, question_text, question_course, question_semester, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issss", $student_id, $question_text, $question_course, $question_semester, $image_path);

    // Execute the statement to insert the question
    if ($stmt->execute()) {
        // Redirect with success message
        $message = "Question posted successfully!";
        header("Location: ask.php?message=" . urlencode($message));
        exit();
    } else {
        // Redirect with error message if insertion into question table fails
        $message = "Error posting question. Please try again.";
        header("Location: ask.php?message=" . urlencode($message));
        exit();
    }

    // Close the statement
    $stmt->close();
    $checkStmt->close();
}

// Close the database connection
$conn->close();
?>
