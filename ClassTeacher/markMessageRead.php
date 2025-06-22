<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if (isset($_POST['messageId'])) {
    $messageId = $_POST['messageId'];
    
    // Update message status to read
    $query = "UPDATE tblmessages SET isRead = 1 
              WHERE id = ? AND receiverId = ? AND receiverType = 'teacher'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $messageId, $_SESSION['userId']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}
?>