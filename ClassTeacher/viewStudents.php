<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get teacher's classes
$teacherId = $_SESSION['userId'];
$query = "SELECT DISTINCT c.Id as classId, c.className, ca.Id as armId, ca.classArmName
          FROM teacher_classes tc
          INNER JOIN tblclass c ON c.Id = tc.class_id
          INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
          WHERE tc.teacher_id = ?
          ORDER BY c.className, ca.classArmName";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$classResult = $stmt->get_result();

// Initialize variables
$selectedClass = isset($_POST['classInfo']) ? $_POST['classInfo'] : '';
$students = null;

if (!empty($selectedClass)) {
    list($classId, $armId) = explode(':', $selectedClass);
    
    // Verify teacher access
    $accessQuery = "SELECT COUNT(*) as count FROM teacher_classes 
                   WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?";
    $stmt = $conn->prepare($accessQuery);
    $stmt->bind_param("iii", $teacherId, $classId, $armId);
    $stmt->execute();
    
    if ($stmt->get_result()->fetch_assoc()['count'] > 0) {
        // Get students for selected class
        $query = "SELECT s.*, c.className, ca.classArmName
                 FROM tblstudents s
                 INNER JOIN tblclass c ON c.Id = s.classId
                 INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
                 WHERE s.classId = ? AND s.classArmId = ?
                 ORDER BY s.firstName";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $classId, $armId);
        $stmt->execute();
        $students = $stmt->get_result();
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
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php";?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php";?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">View Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Students</h6>
                </div>
                <div class="card-body">
                  <form method="post" class="mb-3">
                    <div class="form-row align-items-center">
                      <div class="col-auto">
                        <select name="classInfo" class="form-control mb-2" required>
                          <option value="">--Select Class--</option>
                          <?php 
                          while ($class = $classResult->fetch_assoc()) {
                              $selected = ($selectedClass == $class['classId'].':'.$class['armId']) ? 'selected' : '';
                              echo '<option value="'.$class['classId'].':'.$class['armId'].'" '.$selected.'>'.
                                   htmlspecialchars($class['className'].' - '.$class['classArmName']).'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">View Students</button>
                      </div>
                    </div>
                  </form>

                  <?php if ($students): ?>
                    <div class="table-responsive">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Student ID</th>
                            <th>Stage & Group</th>
                            <th>Subject</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $sn = 0;
                          while ($row = $students->fetch_assoc()) {
                              $sn++;
                              echo "<tr>
                                      <td>".$sn."</td>
                                      <td>".htmlspecialchars($row['firstName'])."</td>
                                      <td>".htmlspecialchars($row['lastName'])."</td>
                                      <td>".htmlspecialchars($row['admissionNumber'])."</td>
                                      <td>".htmlspecialchars($row['className'])."</td>
                                      <td>".htmlspecialchars($row['classArmName'])."</td>
                                    </tr>";
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  <?php elseif (isset($_POST['classInfo'])): ?>
                    <div class="alert alert-info">No students found in this class.</div>
                  <?php endif; ?>
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
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  
  <script>
    $(document).ready(function () {
      $('#dataTableHover').DataTable({
        dom: 'Bfrtip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        responsive: true
      });
    });
  </script>
</body>
</html>