<?php
include '../Includes/dbcon.php';

echo "<h2>Attendance Database Analysis</h2>";

// Check current date
echo "<p><strong>Current Server Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Check what dates we have in attendance table
$dateQuery = "SELECT 
    dateTimeTaken,
    COUNT(*) as count
    FROM tblattendance 
    GROUP BY dateTimeTaken
    ORDER BY dateTimeTaken DESC 
    LIMIT 20";

$result = $conn->query($dateQuery);

echo "<h3>Recent Attendance Dates:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Date</th><th>Record Count</th><th>Days Ago</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $daysAgo = '';
        if (strtotime($row['dateTimeTaken'])) {
            $diff = (strtotime(date('Y-m-d')) - strtotime($row['dateTimeTaken'])) / (60*60*24);
            $daysAgo = round($diff) . ' days ago';
        }
        
        echo "<tr>";
        echo "<td>" . $row['dateTimeTaken'] . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "<td>" . $daysAgo . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No attendance records found!</p>";
}

// Check if there's today's data
echo "<h3>Filter Tests:</h3>";

$today = date('Y-m-d');
$todayQuery = "SELECT COUNT(*) as count FROM tblattendance WHERE dateTimeTaken = '$today'";
$todayResult = $conn->query($todayQuery);
$todayCount = $todayResult ? $todayResult->fetch_assoc()['count'] : 0;
echo "<p><strong>Today ($today):</strong> $todayCount records</p>";

// Check this week's data
$weekQuery = "SELECT COUNT(*) as count FROM tblattendance WHERE STR_TO_DATE(dateTimeTaken, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$weekResult = $conn->query($weekQuery);
$weekCount = $weekResult ? $weekResult->fetch_assoc()['count'] : 0;
echo "<p><strong>This Week:</strong> $weekCount records</p>";

// Check this month's data
$monthQuery = "SELECT COUNT(*) as count FROM tblattendance WHERE STR_TO_DATE(dateTimeTaken, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
$monthResult = $conn->query($monthQuery);
$monthCount = $monthResult ? $monthResult->fetch_assoc()['count'] : 0;
echo "<p><strong>This Month:</strong> $monthCount records</p>";

// Check all data
$allQuery = "SELECT COUNT(*) as count FROM tblattendance";
$allResult = $conn->query($allQuery);
$allCount = $allResult ? $allResult->fetch_assoc()['count'] : 0;
echo "<p><strong>All Time:</strong> $allCount records</p>";

// Suggest solution if no recent data
if ($todayCount == 0 && $weekCount == 0) {
    echo "<div style='background: #ffeb3b; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>⚠️ Notice:</h3>";
    echo "<p>There appears to be no recent attendance data (today or this week).</p>";
    echo "<p>This is why the 'Today' and 'Week' filters show no results.</p>";
    echo "<p>The most recent data is from the dates listed above.</p>";
    echo "</div>";
}

$conn->close();
?>
