<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
include '../Includes/functions.php';

// Get class and arm IDs from URL parameters or session
$classId = isset($_GET['class']) ? $_GET['class'] : $_SESSION['classId'];
$armId = isset($_GET['arm']) ? $_GET['arm'] : $_SESSION['classArmId'];
$teacherId = $_SESSION['userId'];

// Verify teacher has access to this class
$accessQuery = "SELECT tc.*, c.className, ca.classArmName 
               FROM teacher_classes tc
               INNER JOIN tblclass c ON c.Id = tc.class_id
               INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
               WHERE tc.teacher_id = ? 
               AND tc.class_id = ? 
               AND tc.class_arm_id = ?";

$stmt = $conn->prepare($accessQuery);
$stmt->bind_param("iii", $teacherId, $classId, $armId);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    $_SESSION['error'] = "Access denied to this class";
    header("Location: index.php");
    exit();
}

$classInfo = $result->fetch_assoc();

// Store class info in session
$_SESSION['classId'] = $classId;
$_SESSION['classArmId'] = $armId;

// Add after session and dbcon includes
$dateTaken = date("Y-m-d");

// Get number of attendance entries for today - Fixed to handle DATETIME field
$attendanceCheck = "SELECT COUNT(DISTINCT sessionNumber) as entries 
                   FROM tblattendance 
                   WHERE classId = ? 
                   AND classArmId = ? 
                   AND DATE(dateTimeTaken) = ?";

$stmt = $conn->prepare($attendanceCheck);
$stmt->bind_param("iis", $classId, $armId, $dateTaken);
$stmt->execute();
$result = $stmt->get_result();
$attendanceCount = $result->fetch_assoc()['entries'];

// Get next session number (1 or 2)
$nextSession = $attendanceCount + 1;

// Fetch class and arm details based on the teacher
$query = "SELECT tblclass.className, tblclassarms.classArmName 
    FROM tblclassteacher
    INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
    WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$rrw = $rs->fetch_assoc();

// Session and Term details
$sessionQuery = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE isActive = '1'");
$sessionTerm = mysqli_fetch_array($sessionQuery);
$sessionTermId = $sessionTerm['Id'];

if (isset($_POST['save'])) {
    $admissionNo = $_POST['admissionNo'];
    $check = isset($_POST['check']) ? $_POST['check'] : array();
    
    if ($nextSession > 2) {
        $statusMsg = "<div class='alert alert-danger'>Maximum attendance entries (2) for today have been reached.</div>";
    } else {
        try {
            $conn->begin_transaction();
            
            // Insert attendance records - Fixed to use proper datetime
            $insertQuery = "INSERT INTO tblattendance 
                          (admissionNo, status, classId, classArmId, sessionNumber, dateTimeTaken) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertQuery);
            
            // Use current datetime for consistent insertion
            $currentDateTime = date("Y-m-d H:i:s");
            
            // Process all students
            $presentCount = 0;
            $absentCount = 0;
            
            foreach ($admissionNo as $studentId) {
                $status = in_array($studentId, $check) ? 1 : 0;
                if ($status == 1) $presentCount++;
                else $absentCount++;
                
                $stmt->bind_param("siiiss", 
                    $studentId, 
                    $status, 
                    $classId, 
                    $armId, 
                    $nextSession, 
                    $currentDateTime
                );
                $stmt->execute();
            }
            
            $conn->commit();
            
            $statusMsg = "<div class='alert alert-success'>
                Attendance #{$nextSession} taken successfully! 
                (Present: $presentCount, Absent: $absentCount)
            </div>";
            
        } catch (Exception $e) {
            $conn->rollback();
            $statusMsg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="../QIULOGO1.png" rel="icon">
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

    <script>
        function classArmDropdown(str) {
            if (str == "") {
                document.getElementById("txtHint").innerHTML = "";
                return;
            } else {
                if (window.XMLHttpRequest) {
                    // Code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // Code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "Includes/sidebar.php"; ?>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Take Attendance (Today's Date: <?php echo $todaysDate = date("m-d-Y"); ?>)</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Students in Class</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <!-- Input Group -->
                            <form method="post">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card mb-4">
                                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                <h6 class="m-0 font-weight-bold text-primary">
                                                    Taking Attendance for Session #<?php echo $nextSession; ?>
                                                </h6>
                                                <h6 class="m-0 font-weight-bold text-danger">
                                                    Note: <i>Click on the checkboxes beside each student to take attendance!</i>
                                                </h6>
                                            </div>
                                            <div class="table-responsive p-3">
                                                <?php echo $statusMsg; ?>
                                                <table class="table align-items-center table-flush table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>First Name</th>
                                                            <th>Last Name</th>
                                                            <th>Other Name</th>
                                                            <th>Admission No</th>
                                                            <th>Stage & Group</th>
                                                            <th>Subject</th>
                                                            <th>Check</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $studentsQuery = "SELECT tblstudents.Id, tblstudents.admissionNumber, 
                                                                            tblclass.className, tblclass.Id AS classId, 
                                                                            tblclassarms.classArmName, tblclassarms.Id AS classArmId, 
                                                                            tblstudents.firstName, tblstudents.lastName, 
                                                                            tblstudents.otherName, tblstudents.admissionNumber
                                                                         FROM tblstudents
                                                                         INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                                                                         INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
                                                                         WHERE tblstudents.classId = ? 
                                                                         AND tblstudents.classArmId = ?
                                                                         ORDER BY tblstudents.firstName ASC";

                                                        $stmt = $conn->prepare($studentsQuery);
                                                        $stmt->bind_param("ii", $classId, $armId);
                                                        $stmt->execute();
                                                        $rs = $stmt->get_result();
                                                        $num = $rs->num_rows;
                                                        $sn = 0;

                                                        if ($num > 0) {
                                                            while ($student = $rs->fetch_assoc()) {
                                                                $sn++;
                                                                echo "
                                                                    <tr>
                                                                        <td>{$sn}</td>
                                                                        <td>{$student['firstName']}</td>
                                                                        <td>{$student['lastName']}</td>
                                                                        <td>{$student['otherName']}</td>
                                                                        <td>{$student['admissionNumber']}</td>
                                                                        <td>{$student['className']}</td>
                                                                        <td>{$student['classArmName']}</td>
                                                                        <td><input name='check[]' type='checkbox' value='{$student['admissionNumber']}' class='form-control'></td>
                                                                    </tr>";
                                                                echo "<input name='admissionNo[]' value='{$student['admissionNumber']}' type='hidden' class='form-control'>";
                                                            }
                                                        } else {
                                                            echo "<div class='alert alert-danger' role='alert'>No Record Found!</div>";
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <br>
                                                <button type="submit" name="save" class="btn btn-primary">Take Attendance</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row -->
                    </div>
                    <!-- Documentation Link -->
                </div>
                <!-- Container Fluid-->
            </div>
            <!-- Footer -->
            <?php include "Includes/footer.php"; ?>
            <!-- Footer -->
        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable(); // ID From dataTable
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
    </script>
</body>

</html>
