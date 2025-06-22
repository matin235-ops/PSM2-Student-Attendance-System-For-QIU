<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Delete all teacher logs
if(isset($_POST['delete_all'])) {
    $query = "DELETE FROM tblteacherlogs";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "All activity logs deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting logs: " . mysqli_error($conn);
    }

    // Redirect to the correct page (viewTeacherLogs.php)
    header("Location: viewTeacherLogs.php");
    exit();
}

// Delete all messages
if(isset($_POST['delete_all_messages'])) {
    try {
        $adminId = $_SESSION['userId'];
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Delete messages where admin is either sender or receiver
        $query = "DELETE FROM tblmessages 
                  WHERE (receiverId = ? AND receiverType = 'admin') 
                  OR (senderId = ? AND senderType = 'admin')";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $adminId, $adminId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            mysqli_commit($conn);
            $_SESSION['success'] = "All messages deleted successfully!";
        } else {
            throw new Exception("Failed to delete messages");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirect back to messages page
    header("Location: viewMessages.php");
    exit();
}

// Delete single message
if(isset($_POST['delete_message']) && isset($_POST['message_id'])) {
    try {
        $messageId = $_POST['message_id'];
        $adminId = $_SESSION['userId'];
        
        // Delete specific message with admin verification
        $query = "DELETE FROM tblmessages 
                  WHERE id = ? 
                  AND ((receiverId = ? AND receiverType = 'admin') 
                  OR (senderId = ? AND senderType = 'admin'))";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iii", $messageId, $adminId, $adminId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
        } else {
            throw new Exception("Failed to delete message");
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>
