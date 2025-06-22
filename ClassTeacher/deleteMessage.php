<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    $teacherId = $_SESSION['userId'];
    
    // Delete message with verification that it belongs to the current teacher
    $query = "DELETE FROM tblmessages 
              WHERE id = ? 
              AND ((senderId = ? AND senderType = 'teacher') 
                   OR (receiverId = ? AND receiverType = 'teacher'))";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $messageId, $teacherId, $teacherId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>