<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send') {
    $adminId = $_SESSION['userId'];
    $teacherId = $_POST['teacherId'];
    $message = $_POST['message'];
    
    $query = "INSERT INTO tblmessages (senderId, senderType, receiverId, receiverType, message) 
              VALUES (?, 'admin', ?, 'teacher', ?)";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $adminId, $teacherId, $message);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

// Get list of teachers
$query = "SELECT DISTINCT t.Id, t.firstName, t.lastName, 
          GROUP_CONCAT(DISTINCT CONCAT(c.className, ' - ', ca.classArmName) SEPARATOR ', ') as classes
          FROM tblclassteacher t
          LEFT JOIN teacher_classes tc ON t.Id = tc.teacher_id
          LEFT JOIN tblclass c ON tc.class_id = c.Id
          LEFT JOIN tblclassarms ca ON tc.class_arm_id = ca.Id
          WHERE t.firstName IS NOT NULL 
          AND t.lastName IS NOT NULL
          GROUP BY t.Id, t.firstName, t.lastName
          ORDER BY t.firstName, t.lastName";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/QIULOGO1.png" rel="icon">
    <title>Send Message to Teacher</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Send Message</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <?php 
                                    if(isset($success)) {
                                        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $success . '</div>';
                                    } elseif(isset($error)) {
                                        echo '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' . $error . '</div>';
                                    }
                                    ?>
                                    <form method="post">
                                        <div class="form-group">
                                            <label>Select Teacher</label>
                                            <select class="form-control" name="teacherId" required>
                                                <option value="">Select Teacher</option>
                                                <?php 
                                                if($result && $result->num_rows > 0) {
                                                    while($row = $result->fetch_assoc()): 
                                                        $teacherName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                                                        $className = !empty($row['classes']) ? 
                                                                    htmlspecialchars($row['classes']) :
                                                                    'No Class Assigned';
                                                ?>
                                                    <option value="<?php echo $row['Id']; ?>">
                                                        <?php echo $teacherName . " (" . $className . ")"; ?>
                                                    </option>
                                                <?php 
                                                    endwhile;
                                                } else {
                                                    echo "<option disabled>No teachers found</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea class="form-control" name="message" rows="4" required></textarea>
                                        </div>
                                        <button type="submit" name="action" value="send" class="btn btn-primary">Send Message</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>
</html>