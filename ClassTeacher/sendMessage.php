<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if(isset($_POST['action']) && $_POST['action'] == 'send') {
    $senderId = $_SESSION['userId'];
    $message = trim($_POST['message']);
    
    // Insert message
    $query = "INSERT INTO tblmessages (senderId, senderType, receiverId, receiverType, message, created_at) 
              VALUES (?, 'teacher', 1, 'admin', ?, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $senderId, $message);
    
    if($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>