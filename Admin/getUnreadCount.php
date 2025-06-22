<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$adminId = $_SESSION['userId'];
$query = "SELECT COUNT(*) as count FROM tblmessages 
          WHERE receiverId = ? AND receiverType = 'admin' AND isRead = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>