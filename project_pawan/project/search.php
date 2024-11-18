<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "job_portal";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$job_title = isset($_GET['job_title']) ? "%" . mysqli_real_escape_string($conn, $_GET['job_title']) . "%" : '';
$location = isset($_GET['location']) ? "%" . mysqli_real_escape_string($conn, $_GET['location']) . "%" : '';
$skills = isset($_GET['skills']) ? "%" . mysqli_real_escape_string($conn, $_GET['skills']) . "%" : '';
$experience = isset($_GET['experience']) ? mysqli_real_escape_string($conn, $_GET['experience']) : '';
$availability = isset($_GET['availability']) ? mysqli_real_escape_string($conn, $_GET['availability']) : '';
$salary = isset($_GET['salary']) ? mysqli_real_escape_string($conn, $_GET['salary']) : '';
$sql = "SELECT * FROM job_seekers WHERE 
            job_title LIKE ? AND
            location LIKE ? AND
            skills LIKE ?";
if ($experience != '') {
    $sql .= " AND experience LIKE ?";
}
if ($availability != '') {
    $sql .= " AND availability LIKE ?";
}
if ($salary != '') {
    $sql .= " AND salary >= ?";
}
$stmt = $conn->prepare($sql);
if ($experience != '' && $availability != '' && $salary != '') {
    $stmt->bind_param("ssssss", $job_title, $location, $skills, $experience, $availability, $salary);
} elseif ($experience != '' && $availability != '') {
    $stmt->bind_param("sssss", $job_title, $location, $skills, $experience, $availability);
} elseif ($experience != '') {
    $stmt->bind_param("sssss", $job_title, $location, $skills, $experience, $salary);
} else {
    $stmt->bind_param("ssss", $job_title, $location, $skills, $salary);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
        echo "Job Title: " . htmlspecialchars($row['job_title']) . "<br>";
        echo "Location: " . htmlspecialchars($row['location']) . "<br>";
        echo "Skills: " . htmlspecialchars($row['skills']) . "<br>";
        echo "Experience: " . htmlspecialchars($row['experience']) . "<br>";
        echo "Availability: " . htmlspecialchars($row['availability']) . "<br>";
        echo "Salary: $" . number_format($row['salary'], 2) . "<br>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No results found. Try adjusting your search criteria.</p>";
}
$stmt->close();
$conn->close();
?>
