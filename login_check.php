<?php
error_reporting(0);
session_start();

$host = "localhost";
$user = "root";
$password = "";
$db = "doubts";
$data = mysqli_connect($host, $user, $password, $db);

if ($data === false) {
    die("Connection error: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $pass = $_POST['password'];

    // First, check if the user is a student
    $sql_student = "SELECT * FROM student WHERE (student_srn = ? OR student_email = ?) AND password = ?";
    $stmt_student = mysqli_prepare($data, $sql_student);
    mysqli_stmt_bind_param($stmt_student, "sss", $username, $username, $pass);
    mysqli_stmt_execute($stmt_student);
    $result_student = mysqli_stmt_get_result($stmt_student);
    $student_row = mysqli_fetch_array($result_student);

    if ($student_row) {
        // If student found, log in as student
        $_SESSION['username'] = $username;
        $_SESSION['usertype'] = "student";
        $_SESSION['user_id'] = $student_row['student_id']; // Store user ID
        header("location:studenthome.php");
        exit();
    } else {
        // If not a student, check if the user is a teacher
        $sql_teacher = "SELECT * FROM teacher WHERE (teacher_srn = ? OR teacher_email = ?) AND password = ?";
        $stmt_teacher = mysqli_prepare($data, $sql_teacher);
        mysqli_stmt_bind_param($stmt_teacher, "sss", $username, $username, $pass);
        mysqli_stmt_execute($stmt_teacher);
        $result_teacher = mysqli_stmt_get_result($stmt_teacher);
        $teacher_row = mysqli_fetch_array($result_teacher);

        if ($teacher_row) {
            // If teacher found, log in as teacher
            $_SESSION['username'] = $username;
            $_SESSION['usertype'] = "teacher";
            $_SESSION['user_id'] = $teacher_row['teacher_id']; // Store user ID
            header("location:adminhome.php");
            exit();
        } else {
            // If neither student nor teacher is found
            $message = "Username or Password Does not Match";
            $_SESSION['loginMessage'] = $message;
            header("location:login.php");
            exit();
        }
    }
}
?>
