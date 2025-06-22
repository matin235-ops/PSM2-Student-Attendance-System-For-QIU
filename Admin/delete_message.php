<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

header('Content-Type: application/json');

if(isset($_POST['messageId'])) {
    $messageId = intval($_POST['messageId']);
    $userId = $_SESSION['userId'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if user has permission to delete this message
        $query = "DELETE FROM tblmessages 
                  WHERE id = ? AND (senderId = ? OR receiverId = ?)";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $messageId, $userId, $userId);
        
        if($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ]);
        } else {
            throw new Exception("Failed to delete message");
        }
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Message ID not provided'
    ]);
}
$conn->close();