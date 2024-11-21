<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doubts";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filter variables
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$selected_course = isset($_GET['course']) ? $_GET['course'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Query to get semesters that have questions with verified answers
$semesters = $conn->query("
    SELECT DISTINCT q.question_semester
    FROM question q
    LEFT JOIN answer_teacher at ON q.question_id = at.question_id
    LEFT JOIN answer_student as_ ON q.question_id = as_.question_id AND as_.verification_status = 1
    WHERE at.answer_text IS NOT NULL OR as_.answer_text IS NOT NULL
    ORDER BY q.question_semester ASC
");

// Define courses by semester for filtering
$courses_by_semester = [
    "1" => ["Mathematics I", "Physics", "Python", "Electrical", "Mechanical"],
    "2" => ["Mathematics II", "Chemistry", "C Programming", "Electronic Principles and Devices"],
    "3" => ["WT", "DDCO", "AFLL", "SDS", "DSA"],
    "4" => ["CN", "DAA", "LA", "OS", "MPCA"],
    "5" => ["DBMS", "SE", "ML"],
    "6" => ["CC", "Java", "CD"],
];

// Base query to get questions with verified answers from the view
$query = "
    SELECT 
        question_id, question_text, question_semester, question_course, 
        answer_source, answer_text
    FROM 
        verified_answers_view
    WHERE 
        1 = 1"; // Placeholder for further filtering conditions

// Apply semester and course filters if set
if ($selected_semester) {
    $query .= " AND question_semester = '" . $conn->real_escape_string($selected_semester) . "'";
}
if ($selected_course) {
    $query .= " AND question_course = '" . $conn->real_escape_string($selected_course) . "'";
}

// Apply search filter if set
if ($search_query) {
    $search_term = $conn->real_escape_string($search_query);
    $query .= " AND (question_text LIKE '%$search_term%' OR answer_text LIKE '%$search_term%')";
}

$query .= " ORDER BY question_id ASC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions and Answers</title>
    <link rel="stylesheet" type="text/css" href="qa.css">
    <script>
        function updateCourses() {
            const semester = document.getElementById("semester").value;
            const courseSelect = document.getElementById("course");
            courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";

            const coursesBySemester = <?php echo json_encode($courses_by_semester); ?>;

            if (coursesBySemester[semester]) {
                coursesBySemester[semester].forEach(course => {
                    const option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            }
        }

        function removeAllFilters() {
            document.getElementById('semester').value = '';
            document.getElementById('course').value = '';
            document.getElementById('search').value = '';

            document.getElementById('filterForm').submit();
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

<h1>All Questions and Verified Answers</h1>

<!-- Filter and Search Form -->
<form id="filterForm" method="GET" action="questions_with_answers.php">
    <label for="semester">Filter by Semester:</label>
    <select name="semester" id="semester" onchange="updateCourses()">
        <option value="">-- Select Semester --</option>
        <?php while ($row = $semesters->fetch_assoc()) { ?>
            <option value="<?php echo htmlspecialchars($row['question_semester']); ?>" <?php if ($row['question_semester'] == $selected_semester) echo 'selected'; ?>>
                Semester <?php echo htmlspecialchars($row['question_semester']); ?>
            </option>
        <?php } ?>
    </select>

    <label for="course">Filter by Course:</label>
    <select name="course" id="course">
        <option value="">-- Select Course --</option>
    </select>

    <label for="search">Search:</label>
    <input type="text" name="search" id="search" placeholder="Enter keywords" value="<?php echo htmlspecialchars($search_query); ?>">

    <button type="submit">Apply Filters</button>
    <button type="button" onclick="removeAllFilters()">Remove All Filters</button>
</form>

<!-- Display Questions and Answers -->
<?php
if ($result->num_rows > 0) {
    $current_question_id = null;

    echo "<div class='questions'>";

    while ($row = $result->fetch_assoc()) {
        if ($row['question_id'] !== $current_question_id) {
            if ($current_question_id !== null) {
                echo "</ul></div>";
            }

            $current_question_id = $row['question_id'];

            echo "<div class='question'>";
            echo "<h2>Question: " . htmlspecialchars($row['question_text']) . "</h2>";
            echo "<p>Semester: " . htmlspecialchars($row['question_semester']) . "</p>";
            echo "<p>Course: " . htmlspecialchars($row['question_course']) . "</p>";
            echo "<h3>Verified Answers:</h3><ul>";
        }

        if ($row['answer_source'] == 'Teacher') {
            echo "<li><strong>Teacher's Answer:</strong> <p>" . htmlspecialchars($row['answer_text']) . "</p></li>";
        }

        if ($row['answer_source'] == 'Student') {
            echo "<li><strong>Student's Verified Answer:</strong> <p>" . htmlspecialchars($row['answer_text']) . "</p></li>";
        }
    }

    echo "</ul></div>";
    echo "</div>";
} else {
    echo "<p>No questions with verified answers found for the selected filters or search terms.</p>";
}

$conn->close();
?>
</body>
</html>
