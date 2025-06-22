<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$query = "SELECT COUNT(*) as unread FROM tblmessages 
          WHERE receiverId = ? 
          AND receiverType = 'admin' 
          AND isRead = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();
$unread = $result->fetch_assoc()['unread'];

header('Content-Type: application/json');
echo json_encode(['unread' => $unread]);
?>