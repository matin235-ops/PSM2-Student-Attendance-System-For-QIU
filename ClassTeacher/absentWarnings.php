<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Define constants for warnings
define('TOTAL_WEEKS', 15);
define('CLASSES_PER_WEEK', 2);
define('TOTAL_CLASSES', TOTAL_WEEKS * CLASSES_PER_WEEK);

define('FIRST_WARNING_PERCENT', 10);
define('SECOND_WARNING_PERCENT', 12);
define('FINAL_WARNING_PERCENT', 16);
define('DROP_WARNING_PERCENT', 17);

// Get teacher's classes
$teacherId = $_SESSION['userId'];
$classQuery = "SELECT DISTINCT c.Id as classId, c.className, ca.Id as armId, ca.classArmName
               FROM teacher_classes tc
               INNER JOIN tblclass c ON c.Id = tc.class_id
               INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
               WHERE tc.teacher_id = ?";

$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$classResult = $stmt->get_result();

// Initialize variables
$selectedClass = isset($_POST['classInfo']) ? $_POST['classInfo'] : '';
$warningData = null;

if (!empty($selectedClass)) {
    list($classId, $armId) = explode(':', $selectedClass);
    
    // Verify teacher access
    $accessQuery = "SELECT COUNT(*) as count FROM teacher_classes 
                   WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?";
    $stmt = $conn->prepare($accessQuery);
    $stmt->bind_param("iii", $teacherId, $classId, $armId);
    $stmt->execute();
    
    if ($stmt->get_result()->fetch_assoc()['count'] > 0) {
        // Get student attendance data
        $query = "SELECT 
            s.firstName, 
            s.lastName, 
            s.admissionNumber,
            c.className,
            ca.classArmName,
            COUNT(DISTINCT a.dateTimeTaken) as totalDays,
            SUM(CASE WHEN a.status = '0' THEN 1 ELSE 0 END) as absentDays
            FROM tblstudents s
            INNER JOIN tblclass c ON s.classId = c.Id
            INNER JOIN tblclassarms ca ON s.classArmId = ca.Id
            LEFT JOIN tblattendance a ON s.admissionNumber = a.admissionNo 
                AND ca.Id = a.classArmId
            WHERE s.classId = ? AND s.classArmId = ?
            GROUP BY s.admissionNumber, s.firstName, s.lastName, c.className, ca.classArmName
            ORDER BY s.firstName";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $classId, $armId);
        $stmt->execute();
        $warningData = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../QIULOGO1.png" rel="icon">
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .badge-drop {
            background-color: #dc3545;
            color: white;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            50% { opacity: 0.5; }
        }
        .warning-table th {
            background-color: #f8f9fc;
        }
        .warning-table td {
            vertical-align: middle !important;
        }
        .absence-stats {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
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
                <div class="container-fluid">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">View Student Absence Warnings</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" class="mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-md-6">
                                        <select name="classInfo" class="form-control" required>
                                            <option value="">--Select Class--</option>
                                            <?php 
                                            $classResult->data_seek(0);
                                            while ($class = $classResult->fetch_assoc()) {
                                                $selected = ($selectedClass == $class['classId'].':'.$class['armId']) ? 'selected' : '';
                                                echo '<option value="'.$class['classId'].':'.$class['armId'].'" '.$selected.'>'.
                                                     htmlspecialchars($class['className'].' - '.$class['classArmName']).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">View Warnings</button>
                                    </div>
                                </div>
                            </form>

                            <?php if ($warningData && $warningData->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered warning-table" id="dataTableHover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Stage & Group</th>
                                                <th>Subject</th>
                                                <th>Absent Days</th>
                                                <th>Absence Rate</th>
                                                <th>Warning Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sn = 0;
                                            while ($row = $warningData->fetch_assoc()) {
                                                $sn++;
                                                $studentName = $row['firstName'].' '.$row['lastName'];
                                                $absentDays = (int)$row['absentDays'];
                                                $absenceRate = ($absentDays / TOTAL_CLASSES) * 100;
                                                $absenceRate = round($absenceRate, 1);
                                                
                                                // Determine warning status
                                                if ($absenceRate >= DROP_WARNING_PERCENT) {
                                                    $warningStatus = 'DROPPED FROM LECTURE';
                                                    $warningClass = 'badge badge-drop';
                                                } elseif ($absenceRate >= FINAL_WARNING_PERCENT) {
                                                    $warningStatus = 'FINAL WARNING';
                                                    $warningClass = 'badge badge-danger';
                                                } elseif ($absenceRate >= SECOND_WARNING_PERCENT) {
                                                    $warningStatus = 'Second Warning';
                                                    $warningClass = 'badge badge-warning';
                                                } elseif ($absenceRate >= FIRST_WARNING_PERCENT) {
                                                    $warningStatus = 'First Warning';
                                                    $warningClass = 'badge badge-info';
                                                } else {
                                                    $warningStatus = '';
                                                    $warningClass = '';
                                                }
                                                
                                                echo "<tr>
                                                    <td>".$sn."</td>
                                                    <td>".htmlspecialchars($studentName)."</td>
                                                    <td>".htmlspecialchars($row['className'])."</td>
                                                    <td>".htmlspecialchars($row['classArmName'])."</td>
                                                    <td>".$absentDays." / ".TOTAL_CLASSES."</td>
                                                    <td>".$absenceRate."%</td>
                                                    <td>".($warningStatus ? "<span class='$warningClass'>$warningStatus</span>" : "-")."</td>
                                                </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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

    <script>
        $(document).ready(function () {
            var table = $('#dataTableHover').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                responsive: true
            });

            // Highlight rows with drop warnings
            table.on('draw', function() {
                $('.badge-drop').closest('tr').css('background-color', '#ffe6e6');
            });
        });
    </script>
</body>
</html>