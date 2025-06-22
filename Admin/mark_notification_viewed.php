<?php
session_start();
include '../Includes/dbcon.php';

$today = date('Y-m-d');
$query = "SELECT 
    s.firstName, 
    s.lastName,
    c.className,
    ca.classArmName,
    a.dateTimeTaken,
    a.status
FROM tblattendance a
INNER JOIN tblstudents s ON a.admissionNo = s.admissionNumber
INNER JOIN tblclass c ON s.classId = c.Id
INNER JOIN tblclassarms ca ON s.classArmId = ca.Id
WHERE DATE(a.dateTimeTaken) = '$today' AND a.status = '1'
ORDER BY a.dateTimeTaken DESC";

$result = $conn->query($query);
$attendanceData = array();

while ($row = $result->fetch_assoc()) {
    $attendanceData[] = array(
        'student' => $row['firstName'] . ' ' . $row['lastName'],
        'class' => $row['className'] . ' ' . $row['classArmName'],
        'time' => date('h:i A', strtotime($row['dateTimeTaken'])),
        'status' => $row['status']
    );
}

$_SESSION['notification_viewed'] = true;
echo json_encode([
    'success' => true,
    'data' => $attendanceData
]);
?> 