<?php

function logTeacherActivity($conn, $teacherId, $activity) {
    try {
        // Convert teacherId to integer
        $teacherId = (int)$teacherId;
        
        $query = "INSERT INTO tblteacherlogs (teacherId, activity) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $teacherId, $activity);
        
        if (!$stmt->execute()) {
            error_log("Failed to log activity: " . $stmt->error);
            return false;
        }
        return true;
    } catch (Exception $e) {
        error_log("Error in logTeacherActivity: " . $e->getMessage());
        return false;
    }
}

function verifyTeacherLog($conn, $teacherId) {
    // First check if teacher exists
    $checkQuery = "SELECT Id FROM tblclassteacher WHERE Id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $teacherId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Teacher ID $teacherId does not exist");
        return false;
    }

    // If teacher exists, try to create test log
    try {
        $query = "INSERT INTO tblteacherlogs (teacherId, activity) 
                  VALUES (?, 'Test log entry')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teacherId);
        
        if ($stmt->execute()) {
            // Clean up test entry
            $conn->query("DELETE FROM tblteacherlogs WHERE activity = 'Test log entry'");
            return true;
        }
    } catch (Exception $e) {
        error_log("Error creating test log: " . $e->getMessage());
        return false;
    }
    return false;
}
?>