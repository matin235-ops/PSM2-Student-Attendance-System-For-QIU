<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['classId']) && isset($_GET['armId'])) {
    $classId = intval($_GET['classId']);
    $armId = intval($_GET['armId']);
    
    // Verify teacher has access to this class
    $teacherId = $_SESSION['userId'];
    $accessCheck = $conn->prepare("SELECT COUNT(*) as count FROM teacher_classes 
                                 WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?");
    $accessCheck->bind_param("iii", $teacherId, $classId, $armId);
    $accessCheck->execute();
    
    if ($accessCheck->get_result()->fetch_assoc()['count'] > 0) {
        // Get students
        $query = "SELECT admissionNumber, firstName, lastName 
                 FROM tblstudents 
                 WHERE classId = ? AND classArmId = ? 
                 ORDER BY firstName, lastName";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $classId, $armId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<option value=''>--Select Student--</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='".htmlspecialchars($row['admissionNumber'])."'>".
                 htmlspecialchars($row['firstName']." ".$row['lastName']." (".$row['admissionNumber'].")")."</option>";
        }
    } else {
        echo "<option value=''>Access Denied</option>";
    }
} else {
    echo "<option value=''>Invalid Request</option>";
}
?>