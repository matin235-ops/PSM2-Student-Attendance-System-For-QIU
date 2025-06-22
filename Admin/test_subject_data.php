<?php
// Simple test file to check subject attendance data
include '../Includes/dbcon.php';

echo "<h3>Subject Attendance Data Test</h3>";

// Test query to check subject data
$testQuery = "SELECT 
    c.className,
    ca.classArmName as subjectName,
    COUNT(a.id) as total,
    SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
    ROUND((SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 1) as attendance_rate
    FROM tblclass c
    JOIN tblclassarms ca ON c.Id = ca.classId
    LEFT JOIN tblattendance a ON ca.Id = a.classArmId
    GROUP BY c.Id, c.className, ca.Id, ca.classArmName
    HAVING total > 0
    ORDER BY c.className, ca.classArmName";

$result = $conn->query($testQuery);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th style='padding: 8px;'>Class</th>
            <th style='padding: 8px;'>Subject</th>
            <th style='padding: 8px;'>Present</th>
            <th style='padding: 8px;'>Total</th>
            <th style='padding: 8px;'>Attendance Rate</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $attendanceRate = $row['attendance_rate'];
        $rowColor = $attendanceRate >= 80 ? '#d4edda' : ($attendanceRate >= 60 ? '#fff3cd' : '#f8d7da');
        
        echo "<tr style='background-color: {$rowColor};'>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row['className']) . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row['subjectName']) . "</td>";
        echo "<td style='padding: 8px;'>" . $row['present'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['total'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['attendance_rate'] . "%</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h4>Summary:</h4>";
    echo "<p>Total subjects found: " . $result->num_rows . "</p>";
} else {
    echo "<p style='color: red;'>No subject attendance data found</p>";
}

echo "<br><br><h3>Classes and Subjects available:</h3>";

// Check available classes and subjects
$classSubjectQuery = "SELECT 
    c.className,
    ca.classArmName as subjectName
    FROM tblclass c
    JOIN tblclassarms ca ON c.Id = ca.classId
    ORDER BY c.className, ca.classArmName";

$classSubjectResult = $conn->query($classSubjectQuery);
if ($classSubjectResult && $classSubjectResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #e9ecef;'>
            <th style='padding: 8px;'>Class</th>
            <th style='padding: 8px;'>Subject</th>
          </tr>";
    
    while ($row = $classSubjectResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row['className']) . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($row['subjectName']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No classes or subjects found</p>";
}

echo "<br><br><h3>Test Different Filters:</h3>";
echo "<p><a href='get_subject_data.php' target='_blank'>Test All Time Filter</a></p>";
echo "<p>To test other filters, you can manually POST to get_subject_data.php with filter parameters.</p>";

$conn->close();
?>
