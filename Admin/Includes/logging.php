<?php
function logActivity($message, $type = 'system') {
    $logFile = '../logs/system.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp][$type] $message\n";
    
    // Create logs directory if it doesn't exist
    if (!file_exists('../logs')) {
        mkdir('../logs', 0777, true);
    }
    
    // Write to log file
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Example usage in different parts of the system:
// logActivity("Teacher ID: $teacherId logged in", "login");
// logActivity("Attendance taken for Class: $className", "attendance");
// logActivity("Profile updated for Teacher: $teacherName", "profile");
?> 