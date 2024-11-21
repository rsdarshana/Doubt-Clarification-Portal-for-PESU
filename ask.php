<?php
session_start();
$message = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a Question</title>
    <link rel="stylesheet" type="text/css" href="ask.css">
    <script>
        function updateCourses() {
            const semester = document.getElementById("semester").value;
            const courseSelect = document.getElementById("course");
            courseSelect.innerHTML = ""; // Clear existing options

            const coursesBySemester = {
                "1": ["Mathematics I", "Physics", "Python", "Electrical", "Mechanical"],
                "2": ["Mathematics II", "Chemistry", "C Programming", "Electronic Principles and Devices"],
                "3": ["WT", "DDCO", "AFLL", "SDS", "DSA"],
                "4": ["CN", "DAA", "LA", "OS", "MPCA"],
                "5": ["DBMS", "SE", "ML"],
                "6": ["CC", "Java", "CD"]
            };

            // Populate courses based on selected semester
            if (coursesBySemester[semester]) {
                coursesBySemester[semester].forEach(course => {
                    const option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            } else {
                const defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.textContent = "-- Select Course --";
                courseSelect.appendChild(defaultOption);
            }
        }

        // Function to display the selected image file name
        function showFileName() {
            const fileInput = document.getElementById("image-upload");
            const fileNameDisplay = document.getElementById("file-name");
            fileNameDisplay.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : "";
        }
    </script>
</head>
<body>
    <header class="header">
        <a href="studenthome.php" class="dashboard-title">Student Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>

    <main class="content">
        <h1>Ask a Question</h1>

        <!-- Display message after form submission -->
        <?php if ($message) { ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="submit_question.php" method="POST" enctype="multipart/form-data" class="ask-form">
            <div class="textarea-container">
                <textarea name="question_text" id="question_text" rows="4" cols="50" placeholder="Ask your question here..." required></textarea>
                <label for="image-upload" class="image-upload-btn">📷</label>
                <input type="file" id="image-upload" name="image" accept="image/*" onchange="showFileName()">
                <span id="file-name" class="file-name"></span>
            </div>
            
            <label for="semester">Select Semester:</label>
            <select name="question_semester" id="semester" onchange="updateCourses()" required>
                <option value="">-- Select Semester --</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
                <option value="7">Semester 7</option>
                <option value="8">Semester 8</option>
            </select>
            
            <label for="course">Select Course:</label>
            <select name="question_course" id="course" required>
                <option value="">-- Select Course --</option>
            </select>
            
            <button type="submit" class="btn btn-submit">Post Question</button>
        </form>
    </main>
</body>
</html>
