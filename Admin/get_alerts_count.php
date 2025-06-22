<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

header('Content-Type: application/json');

$today = date('Y-m-d');

// Get attendance count
$queryAttendance = "SELECT COUNT(*) as count FROM tblattendance 
                    WHERE dateTimeTaken LIKE '$today%'";
$rsAttendance = $conn->query($queryAttendance);
$attendanceCount = $rsAttendance->fetch_assoc()['count'];

// Get unread messages count
$queryMessages = "SELECT COUNT(*) as count FROM tblmessages 
                 WHERE receiverType = 'admin' 
                 AND isRead = 0";
$rsMessages = $conn->query($queryMessages);
$unreadMessages = $rsMessages->fetch_assoc()['count'];

echo json_encode([
    'attendance' => $attendanceCount,
    'messages' => $unreadMessages,
    'total' => $attendanceCount + $unreadMessages
]);