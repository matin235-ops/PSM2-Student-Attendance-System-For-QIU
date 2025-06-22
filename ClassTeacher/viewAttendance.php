<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get teacher's classes
$teacherId = $_SESSION['userId'];
$classQuery = "SELECT DISTINCT c.Id as classId, c.className, ca.Id as armId, ca.classArmName
               FROM teacher_classes tc
               INNER JOIN tblclass c ON c.Id = tc.class_id
               INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
               WHERE tc.teacher_id = ?
               ORDER BY c.className, ca.classArmName";

$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$classResult = $stmt->get_result();

// Store classes in an array for reuse
$classes = [];
while ($class = $classResult->fetch_assoc()) {
    $classes[] = $class;
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
  
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
  </style>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
      <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">View Class Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Class Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Class Attendance</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                            <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                            <select required name="classId" class="form-control mb-3">
                                <option value="">--Select Class--</option>
                                <?php 
                                foreach ($classes as $class) {
                                    echo '<option value="'.$class['classId'].':'.$class['armId'].'">'.
                                         htmlspecialchars($class['className'].' - '.$class['classArmName']).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                            <input type="date" class="form-control" name="dateTaken" required>
                        </div>
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Class Attendance</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Student ID</th>
                        <th>Stage & Group</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                   
                    <tbody>

                  <?php
                    if(isset($_POST['view'])){
                        $dateTaken = $_POST['dateTaken'];
                        list($classId, $armId) = explode(':', $_POST['classId']);
                        
                        if(empty($dateTaken) || empty($classId) || empty($armId)){
                            echo "<div class='alert alert-danger'>Please select both class and date</div>";
                            exit();
                        }

                        // Verify teacher has access to this class
                        $accessQuery = "SELECT COUNT(*) as count FROM teacher_classes 
                                       WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?";
                        $stmt = $conn->prepare($accessQuery);
                        $stmt->bind_param("iii", $teacherId, $classId, $armId);
                        $stmt->execute();
                        if ($stmt->get_result()->fetch_assoc()['count'] == 0) {
                            echo "<div class='alert alert-danger'>Access denied to this class</div>";
                            exit();
                        }

                        // Get attendance records
                        $query = "SELECT s.firstName, s.lastName, s.admissionNumber,
                                  c.className, ca.classArmName,
                                  MAX(CASE WHEN a.sessionNumber = 1 THEN a.status END) as session1,
                                  MAX(CASE WHEN a.sessionNumber = 2 THEN a.status END) as session2,
                                  DATE(a.dateTimeTaken) as dateTaken
                                  FROM tblstudents s
                                  INNER JOIN tblclass c ON c.Id = s.classId
                                  INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
                                  LEFT JOIN tblattendance a ON (s.admissionNumber = a.admissionNo 
                                      AND DATE(a.dateTimeTaken) = ?)
                                  WHERE s.classId = ? AND s.classArmId = ?
                                  GROUP BY s.admissionNumber
                                  ORDER BY s.firstName ASC";

                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("sii", $dateTaken, $classId, $armId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if($result->num_rows > 0) {
                            $sn = 0;
                            while ($rows = $result->fetch_assoc()) {
                                $sn++;
                                $statusDisplay = '';
                                
                                // Handle Session 1
                                if (!is_null($rows['session1'])) {
                                    $statusClass = ($rows['session1'] == '1') ? 'badge badge-success' : 'badge badge-danger';
                                    $statusText = ($rows['session1'] == '1') ? 'Present' : 'Absent';
                                    $statusDisplay .= "<span class='{$statusClass}'>S1: {$statusText}</span> ";
                                }
                                
                                // Handle Session 2
                                if (!is_null($rows['session2'])) {
                                    $statusClass = ($rows['session2'] == '1') ? 'badge badge-success' : 'badge badge-danger';
                                    $statusText = ($rows['session2'] == '1') ? 'Present' : 'Absent';
                                    $statusDisplay .= "<span class='{$statusClass}'>S2: {$statusText}</span> ";
                                }

                                // Only show row if attendance was taken
                                if ($statusDisplay) {
                                    echo "<tr>
                                        <td>".$sn."</td>
                                        <td>".htmlspecialchars($rows['firstName'])."</td>
                                        <td>".htmlspecialchars($rows['lastName'])."</td>
                                        <td>".htmlspecialchars($rows['admissionNumber'])."</td>
                                        <td>".htmlspecialchars($rows['className'])."</td>
                                        <td>".htmlspecialchars($rows['classArmName'])."</td>
                                        <td>".$statusDisplay."</td>
                                        <td>".htmlspecialchars($dateTaken)."</td>
                                    </tr>";
                                }
                            }
                            
                            if ($sn == 0) {
                                echo "<tr><td colspan='8'><div class='alert alert-info' role='alert'>
                                No attendance records found for date: ".htmlspecialchars($dateTaken)."
                                </div></td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'><div class='alert alert-info' role='alert'>
                            No students found in this class.
                            </div></td></tr>";
                        }
                    }
                  ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>

            <!-- Download Attendance Report -->
            <div class="card mb-4" id="downloadReport">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Download Attendance Report</h6>
                </div>
                <div class="card-body">
                    <form method="get" action="downloadRecord.php" class="mb-4">
                        <div class="form-row">
                            <div class="col-md-4">
                                <label>Select Class</label>
                                <select name="classInfo" class="form-control" required>
                                    <option value="">Select Class</option>
                                    <?php 
                                    foreach ($classes as $class) {
                                        echo '<option value="'.$class['classId'].':'.$class['armId'].'">'.
                                             htmlspecialchars($class['className'].' - '.$class['classArmName']).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="from" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="to" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-download mr-2"></i>Download
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
          </div>
          <!--Row-->

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
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

    // Scroll to download section if URL contains #downloadReport
    if(window.location.hash === '#downloadReport') {
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('downloadReport');
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Add highlight effect
            element.style.animation = 'highlight 2s';
        });
    }
  </script>
</body>

</html>