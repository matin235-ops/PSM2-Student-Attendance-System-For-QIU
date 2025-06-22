<?php
// Remove error suppression to see potential errors during development
// error_reporting(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../img/QIULOGO1.png" rel="icon"> <!-- Fixed icon path -->
    <title>Student Absence Warnings</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
    
    <style>
        .badge-drop {
            background-color: #dc3545;
            color: white;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            50% { opacity: 0.5; }
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
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Student Absence Monitoring</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Absence Warnings</li>
                        </ol>

                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="m-0 font-weight-bold text-primary mb-3">Filter Absences</h6>
                                    <select class="form-control" id="subjectFilter">
                                        <option value="">All Subjects</option>
                                        <?php
                                        $subjectQuery = "SELECT DISTINCT ca.Id, ca.classArmName 
                                                       FROM tblclassarms ca 
                                                       ORDER BY ca.classArmName";
                                        $subjectResult = mysqli_query($conn, $subjectQuery);
                                        while($subject = mysqli_fetch_assoc($subjectResult)) {
                                            echo "<option value='".$subject['classArmName']."'>";
                                            echo htmlspecialchars($subject['classArmName']);
                                            echo "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Students Absence Status</h6>
                                </div>
                                <div class="table-responsive p-3">
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
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
                                            // Define constants
                                            define('TOTAL_WEEKS', 15);
                                            define('CLASSES_PER_WEEK', 2);
                                            define('TOTAL_CLASSES', TOTAL_WEEKS * CLASSES_PER_WEEK);

                                            define('FIRST_WARNING_PERCENT', 10);
                                            define('SECOND_WARNING_PERCENT', 12);
                                            define('FINAL_WARNING_PERCENT', 16);
                                            define('DROP_WARNING_PERCENT', 17);

                                            // Modify the query to handle potential NULL values and add error checking
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
                                                    AND ca.Id = a.classArmId  # This ensures absences are counted per class
                                                GROUP BY 
                                                    s.admissionNumber,
                                                    s.firstName,
                                                    s.lastName,
                                                    c.className,
                                                    ca.classArmName
                                                ORDER BY c.className, ca.classArmName, s.firstName";

                                            // Execute query with error checking
                                            $rs = mysqli_query($conn, $query);
                                            if (!$rs) {
                                                die("Query failed: " . mysqli_error($conn));
                                            }

                                            $sn = 0;
                                            $currentStudent = '';
                                            
                                            while($row = mysqli_fetch_assoc($rs)) {
                                                $sn++;
                                                $studentName = $row['firstName'].' '.$row['lastName'];
                                                $absentDays = (int)$row['absentDays'];
                                                $totalDays = max((int)$row['totalDays'], 1); // Prevent division by zero
                                                $absenceRate = ($absentDays / TOTAL_CLASSES) * 100;
                                                $absenceRate = round($absenceRate, 1);
                                                
                                                // Determine warning status for this specific class
                                                if($absenceRate >= DROP_WARNING_PERCENT) {
                                                    $warningStatus = 'DROPPED FROM LECTURE';
                                                    $warningClass = 'badge badge-drop';
                                                } elseif($absenceRate >= FINAL_WARNING_PERCENT) {
                                                    $warningStatus = 'FINAL WARNING';
                                                    $warningClass = 'badge badge-danger';
                                                } elseif($absenceRate >= SECOND_WARNING_PERCENT) {
                                                    $warningStatus = 'Second Warning';
                                                    $warningClass = 'badge badge-warning';
                                                } elseif($absenceRate >= FIRST_WARNING_PERCENT) {
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
                            </div>
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
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#dataTableHover').DataTable({
                order: [[2, 'asc'], [5, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true
            });

            // Add custom filter for subjects
            $('#subjectFilter').on('change', function() {
                var subject = $(this).val();
                table.column(3) // Column index for Subject
                    .search(subject)
                    .draw();
            });

            // Add custom styling for warning badges
            table.on('draw', function() {
                $('.badge-drop').closest('tr').css('background-color', '#ffe6e6');
            });
        });
    </script>
</body>
</html>