<?php
// Test script to validate session comparison query
include '../Includes/dbcon.php';

echo "<h2>Session Comparison Query Test</h2>";

// Define the session comparison query
$sessionCompQuery = "SELECT 
    CONCAT(c.className, ' - ', ca.classArmName) as class_subject,
    c.className,
    ca.classArmName,
    ca.Id as classArmId,
    
    -- Session 1 data
    SUM(CASE WHEN a.sessionNumber = 1 AND a.status = '1' THEN 1 ELSE 0 END) as present_s1,
    SUM(CASE WHEN a.sessionNumber = 1 AND a.status = '0' THEN 1 ELSE 0 END) as absent_s1,
    SUM(CASE WHEN a.sessionNumber = 1 THEN 1 ELSE 0 END) as total_s1,
    
    -- Session 2 data
    SUM(CASE WHEN a.sessionNumber = 2 AND a.status = '1' THEN 1 ELSE 0 END) as present_s2,
    SUM(CASE WHEN a.sessionNumber = 2 AND a.status = '0' THEN 1 ELSE 0 END) as absent_s2,
    SUM(CASE WHEN a.sessionNumber = 2 THEN 1 ELSE 0 END) as total_s2,
    
    -- Total records 
    COUNT(a.Id) as total_records
    
FROM tblclass c
JOIN tblclassarms ca ON c.Id = ca.classId
LEFT JOIN tblattendance a ON ca.Id = a.classArmId
GROUP BY c.Id, ca.Id, c.className, ca.classArmName
HAVING (total_s1 > 0 OR total_s2 > 0)
ORDER BY c.className, ca.classArmName";

echo "<h3>Query:</h3>";
echo "<pre>" . htmlspecialchars($sessionCompQuery) . "</pre>";

$sessionResult = $conn->query($sessionCompQuery);

if ($sessionResult) {
    echo "<h3>Results:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Class-Subject</th>";
    echo "<th>Session 1 Present</th>";
    echo "<th>Session 1 Total</th>";
    echo "<th>Session 1 Rate</th>";
    echo "<th>Session 2 Present</th>";
    echo "<th>Session 2 Total</th>";
    echo "<th>Session 2 Rate</th>";
    echo "</tr>";
    
    if ($sessionResult->num_rows > 0) {
        while($row = $sessionResult->fetch_assoc()) {
            $s1Rate = ($row['total_s1'] > 0) ? ($row['present_s1'] / $row['total_s1']) * 100 : 0;
            $s2Rate = ($row['total_s2'] > 0) ? ($row['present_s2'] / $row['total_s2']) * 100 : 0;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['class_subject']) . "</td>";
            echo "<td>" . $row['present_s1'] . "</td>";
            echo "<td>" . $row['total_s1'] . "</td>";
            echo "<td>" . number_format($s1Rate, 1) . "%</td>";
            echo "<td>" . $row['present_s2'] . "</td>";
            echo "<td>" . $row['total_s2'] . "</td>";
            echo "<td>" . number_format($s2Rate, 1) . "%</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No data found</td></tr>";
    }
    echo "</table>";
} else {
    echo "<h3 style='color: red;'>Query Error:</h3>";
    echo "<p>" . $conn->error . "</p>";
}

// Also test a simple query to see what data is available
echo "<h3>Sample Attendance Data:</h3>";
$sampleQuery = "SELECT 
    a.admissionNo, 
    c.className, 
    ca.classArmName, 
    a.sessionNumber, 
    a.status, 
    a.dateTimeTaken 
FROM tblattendance a
JOIN tblclass c ON a.classId = c.Id
JOIN tblclassarms ca ON a.classArmId = ca.Id
ORDER BY a.dateTimeTaken DESC
LIMIT 10";

$sampleResult = $conn->query($sampleQuery);
if ($sampleResult && $sampleResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Student</th><th>Class</th><th>Subject</th><th>Session</th><th>Status</th><th>Date</th>";
    echo "</tr>";
    
    while($row = $sampleResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['admissionNo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['className']) . "</td>";
        echo "<td>" . htmlspecialchars($row['classArmName']) . "</td>";
        echo "<td>" . $row['sessionNumber'] . "</td>";
        echo "<td>" . ($row['status'] == '1' ? 'Present' : 'Absent') . "</td>";
        echo "<td>" . $row['dateTimeTaken'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No sample data found or query error: " . $conn->error . "</p>";
}

$conn->close();
?>
