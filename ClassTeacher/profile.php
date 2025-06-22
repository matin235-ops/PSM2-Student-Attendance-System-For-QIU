<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

$query = "SELECT * FROM tblclassteacher WHERE Id = ".$_SESSION['userId'];
$rs = $conn->query($query);
$row = $rs->fetch_assoc();

if(isset($_POST['update'])){
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
        logTeacherActivity($conn, $_SESSION['userId'], 
            "Updated profile information - Name: $firstName $lastName");
        $statusMsg = "<div class='alert alert-success'>Profile updated successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../QIULOGO1.png" rel="icon">
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php";?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php";?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Profile Settings</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Update Profile</h6>
                                </div>
                                <div class="card-body">
                                    <div id="statusMsg"></div>
                                    <form id="profileForm" method="post">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="firstName" value="<?php echo $row['firstName']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="lastName" value="<?php echo $row['lastName']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input type="email" class="form-control" name="emailAddress" value="<?php echo $row['emailAddress']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" class="form-control" name="phoneNo" value="<?php echo $row['phoneNo']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>New Password (leave blank to keep current password)</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                        <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "Includes/footer.php";?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'update_profile.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        let data = JSON.parse(response);
                        let messageClass = data.status === 'success' ? 'alert alert-success' : 'alert alert-danger';
                        $('#statusMsg').html(`<div class="${messageClass}">${data.message}</div>`);
                        
                        // Clear the message after 3 seconds
                        setTimeout(function() {
                            $('#statusMsg').html('');
                        }, 3000);
                    },
                    error: function() {
                        $('#statusMsg').html('<div class="alert alert-danger">An error occurred!</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>