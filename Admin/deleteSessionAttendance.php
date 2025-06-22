<?php
header('Content-Type: application/json');
include '../Includes/dbcon.php';
include '../Includes/session.php';

$response = array();

try {
    if (isset($_POST['clearSession'])) {
        $query = "DELETE FROM tblattendance WHERE DATE(dateTimeTaken) = CURDATE()";
        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = "Session attendance cleared successfully";
        } else {
            throw new Exception($conn->error);
        }
    } 
    elseif (isset($_POST['deleteAll'])) {
        $query = "DELETE FROM tblattendance";
        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = "All attendance records deleted successfully";
        } else {
            throw new Exception($conn->error);
        }
    }
    else {
        throw new Exception("Invalid request");
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
