<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

header('Content-Type: application/json');

try {
    $teacherId = $_SESSION['userId'];
    $query = "SELECT m.*, m.id as messageId, m.isRead,
              CASE 
                WHEN m.senderType = 'admin' THEN 'Admin'
                WHEN m.senderType = 'teacher' THEN 'You'
              END as senderName
              FROM tblmessages m
              WHERE ((m.receiverId = ? AND m.receiverType = 'teacher')
              OR (m.senderId = ? AND m.senderType = 'teacher'))
              AND m.deleted_by_teacher = 0
              ORDER BY m.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $teacherId, $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = array();
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>