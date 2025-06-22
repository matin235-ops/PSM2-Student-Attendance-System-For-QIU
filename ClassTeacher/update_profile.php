<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    $password = $_POST['password'];

    // Only update password if a new one is provided
    if(!empty($password)){
        $hashedPassword = md5($password);
        $query = "UPDATE tblclassteacher SET 
                firstName='$firstName', 
                lastName='$lastName',
                emailAddress='$emailAddress',
                phoneNo='$phoneNo',
                password='$hashedPassword'
                WHERE Id = ".$_SESSION['userId'];
    } else {
        $query = "UPDATE tblclassteacher SET 
                firstName='$firstName', 
                lastName='$lastName',
                emailAddress='$emailAddress',
                phoneNo='$phoneNo'
                WHERE Id = ".$_SESSION['userId'];
    }

    if($conn->query($query)){
        $response['status'] = 'success';
        $response['message'] = 'Profile updated successfully!';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'An error occurred!';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method!';
}

echo json_encode($response);
?> 