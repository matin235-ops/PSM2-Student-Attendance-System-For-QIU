<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];
$date = $_GET['date'];
$classInfo = isset($_GET['classInfo']) ? $_GET['classInfo'] : null;

if ($classInfo) {
    list($classId, $armId) = explode(':', $classInfo);
    
    $query = "SELECT 
        COUNT(DISTINCT s.admissionNumber) as totalStudents,
        COUNT(DISTINCT CASE WHEN a.status = '1' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN s.admissionNumber END) as presentSession1,
        COUNT(DISTINCT CASE WHEN a.status = '0' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN s.admissionNumber END) as absentSession1,
        COUNT(DISTINCT CASE WHEN a.status = '1' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN s.admissionNumber END) as presentSession2,
        COUNT(DISTINCT CASE WHEN a.status = '0' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN s.admissionNumber END) as absentSession2,
        SUM(CASE WHEN DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN 1 ELSE 0 END) > 0 as session1Marked,
        SUM(CASE WHEN DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN 1 ELSE 0 END) > 0 as session2Marked
    FROM tblstudents s
    LEFT JOIN tblattendance a ON a.admissionNo = s.admissionNumber 
    WHERE s.classId = ? AND s.classArmId = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssii", $date, $date, $date, $date, $date, $date, $classId, $armId);
} else {
    // Query for all classes
    $query = "SELECT 
        COUNT(DISTINCT s.admissionNumber) as totalStudents,
        COUNT(DISTINCT CASE WHEN a.status = '1' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN s.admissionNumber END) as presentSession1,
        COUNT(DISTINCT CASE WHEN a.status = '0' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN s.admissionNumber END) as absentSession1,
        COUNT(DISTINCT CASE WHEN a.status = '1' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN s.admissionNumber END) as presentSession2,
        COUNT(DISTINCT CASE WHEN a.status = '0' AND DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN s.admissionNumber END) as absentSession2,
        SUM(CASE WHEN DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 1 THEN 1 ELSE 0 END) > 0 as session1Marked,
        SUM(CASE WHEN DATE(a.dateTimeTaken) = ? AND a.sessionNumber = 2 THEN 1 ELSE 0 END) > 0 as session2Marked
    FROM teacher_classes tc
    INNER JOIN tblstudents s ON s.classId = tc.class_id AND s.classArmId = tc.class_arm_id
    LEFT JOIN tblattendance a ON a.admissionNo = s.admissionNumber 
    WHERE tc.teacher_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $date, $date, $date, $date, $date, $date, $teacherId);
}

$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Calculate attendance rates
$totalPresent = ($stats['presentSession1'] ?? 0) + ($stats['presentSession2'] ?? 0);
$totalSessions = (($stats['session1Marked'] ? 1 : 0) + ($stats['session2Marked'] ? 1 : 0)) * $stats['totalStudents'];
$attendanceRate = $totalSessions > 0 ? round(($totalPresent / $totalSessions) * 100, 1) : 0;

// Add additional info to stats array
$stats['attendanceRate'] = $attendanceRate;
$stats['rateClass'] = $attendanceRate >= 90 ? 'text-success' : ($attendanceRate >= 70 ? 'text-warning' : 'text-danger');
$stats['showDetails'] = true;

header('Content-Type: application/json');
echo json_encode($stats);
?>