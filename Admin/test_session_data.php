<?php
// Simple test file to check session data
include '../Includes/dbcon.php';

echo "<h3>Session Data Test</h3>";

// Test query to check session data
$testQuery = "SELECT 
    c.className,
    a.sessionNumber,
    a.status,
    COUNT(*) as count
    FROM tblclass c
    LEFT JOIN tblattendance a ON c.Id = a.classId
    WHERE a.sessionNumber IS NOT NULL
    GROUP BY c.className, a.sessionNumber, a.status
    ORDER BY c.className, a.sessionNumber, a.status";

$result = $conn->query($testQuery);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Class</th><th>Session</th><th>Status</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['className'] . "</td>";
        echo "<td>" . $row['sessionNumber'] . "</td>";
        echo "<td>" . ($row['status'] == 1 ? 'Present' : 'Absent') . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No session data found";
}

echo "<br><br><h3>Classes without session data:</h3>";

// Check classes without session data
$noSessionQuery = "SELECT c.className 
    FROM tblclass c
    LEFT JOIN tblattendance a ON c.Id = a.classId
    WHERE a.sessionNumber IS NULL OR a.sessionNumber = ''
    GROUP BY c.className";

$noSessionResult = $conn->query($noSessionQuery);
if ($noSessionResult && $noSessionResult->num_rows > 0) {
    while ($row = $noSessionResult->fetch_assoc()) {
        echo $row['className'] . "<br>";
    }
} else {
    echo "All classes have session data";
}

$conn->close();
?>
