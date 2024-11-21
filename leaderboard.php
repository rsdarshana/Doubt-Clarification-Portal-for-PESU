<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" type="text/css" href="leaderboard.css">
</head>
<body>
    <header class="header">
        <a href="studenthome.php" class="dashboard-title">Student Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </header>
    <div>
        <h2>Verified Answers Leaderboard</h2>
        <table id="leaderboard">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student SRN</th>
                    <th>Email</th>
                    <th>Verified Answers</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body">
                <!-- Leaderboard entries will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('fetch_leaderboard.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById("leaderboard-body");
                    tbody.innerHTML = ''; // Clear existing data
                    data.forEach((entry, index) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${entry.student_srn}</td>
                            <td>${entry.student_email}</td>
                            <td>${entry.verified_answers}</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching leaderboard:', error));
        });
    </script>

</body>
</html>