<?php
include 'dbcon.php';
include 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['userId'];
    $userType = $_POST['userType'];
    
    if ($userType === 'admin') {
        $query = "DELETE FROM tblmessages WHERE receiverId = ? AND receiverType = 'admin' 
                  OR senderId = ? AND senderType = 'admin'";
    } else {
        $query = "DELETE FROM tblmessages WHERE receiverId = ? AND receiverType = 'teacher' 
                  OR senderId = ? AND senderType = 'teacher'";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
?>