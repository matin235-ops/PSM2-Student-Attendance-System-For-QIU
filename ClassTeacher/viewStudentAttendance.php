<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Add after session check
$teacherId = $_SESSION['userId'];

// Get teacher's classes
$classQuery = "SELECT DISTINCT c.Id as classId, c.className, ca.Id as armId, ca.classArmName
               FROM teacher_classes tc
               INNER JOIN tblclass c ON c.Id = tc.class_id
               INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
               WHERE tc.teacher_id = ?";

$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$classResult = $stmt->get_result();
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
    .badge {
        font-size: 0.9em;
        padding: 5px 10px;
        margin: 2px;
        display: inline-block;
    }
    .badge-success {
        background-color: #1cc88a;
        color: white;
    }
    .badge-danger {
        background-color: #e74a3b;
        color: white;
    }
  </style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we have a previously selected class
        const selectedClass = '<?php echo isset($_POST["classInfo"]) ? $_POST["classInfo"] : ""; ?>';
        if (selectedClass) {
            // Reload the student list for the selected class
            getStudents(selectedClass);
            
            // After loading students, set the previously selected student
            const selectedStudent = '<?php echo isset($_POST["admissionNumber"]) ? $_POST["admissionNumber"] : ""; ?>';
            if (selectedStudent) {
                setTimeout(() => {
                    const studentList = document.getElementById('studentList');
                    if (studentList) {
                        studentList.value = selectedStudent;
                    }
                }, 500); // Small delay to ensure the student list is loaded
            }
        }
        
        // Store the selected type
        const selectedType = '<?php echo isset($_POST["type"]) ? $_POST["type"] : ""; ?>';
        if (selectedType) {
            const typeSelect = document.querySelector('select[name="type"]');
            if (typeSelect) {
                typeSelect.value = selectedType;
                typeDropDown(selectedType);
            }
        }
    });

    function typeDropDown(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else { 
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","ajaxCallTypes.php?tid="+str,true);
            xmlhttp.send();
        }
    }

    function getStudents(classInfo) {
        if (classInfo == "") {
            document.getElementById("studentList").innerHTML = "<option value=''>--First Select Class--</option>";
            return;
        }
        
        const [classId, armId] = classInfo.split(':');
        
        // Show loading state
        document.getElementById("studentList").innerHTML = "<option value=''>Loading students...</option>";
        
        // Store the currently selected student if any
        const currentStudent = document.getElementById("studentList").value;
        
        fetch(`getStudents.php?classId=${classId}&armId=${armId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById("studentList").innerHTML = data;
                
                // If there was a previously selected student, try to reselect it
                if (currentStudent) {
                    const studentList = document.getElementById("studentList");
                    studentList.value = currentStudent;
                }
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                document.getElementById("studentList").innerHTML = "<option value=''>Error loading students</option>";
            });
    }

    function preventEnterSubmit(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            return false;
        }
    }

    function saveFormState() {
        // The form will submit normally
        return true;
    }
</script>

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
            <h1 class="h3 mb-0 text-gray-800">View Student Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Student Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Student Attendance</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post" onsubmit="return saveFormState()" onkeydown="return preventEnterSubmit(event)">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                            <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                            <select required name="classInfo" class="form-control mb-3" onchange="getStudents(this.value)" id="classSelect">
                                <option value="">--Select Class--</option>
                                <?php 
                                $classResult->data_seek(0);
                                while ($class = $classResult->fetch_assoc()) {
                                    $selected = (isset($_POST['classInfo']) && $_POST['classInfo'] == $class['classId'].':'.$class['armId']) ? 'selected' : '';
                                    echo '<option value="'.$class['classId'].':'.$class['armId'].'" '.$selected.'>'.
                                         htmlspecialchars($class['className'].' - '.$class['classArmName']).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Select Student<span class="text-danger ml-2">*</span></label>
                            <select required name="admissionNumber" id="studentList" class="form-control mb-3">
                                <option value="">--First Select Class--</option>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Type<span class="text-danger ml-2">*</span></label>
                            <select required name="type" onchange="typeDropDown(this.value)" class="form-control mb-3">
                                <option value="">--Select--</option>
                                <option value="1">All</option>
                                <option value="2">By Single Date</option>
                                <option value="3">By Date Range</option>
                            </select>
                        </div>
                    </div>
                      <?php
                        echo"<div id='txtHint'></div>";
                      ?>
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
                        $admissionNumber = trim($_POST['admissionNumber']);
                        $type = $_POST['type'];
                        list($classId, $armId) = explode(':', $_POST['classInfo']);

                        if(empty($admissionNumber) || empty($classId) || empty($armId)){
                            echo "<div class='alert alert-danger' role='alert'>Please select both class and student</div>";
                            exit;
                        }

                        // Verify teacher has access to this class
                        $accessCheck = $conn->prepare("SELECT COUNT(*) as count FROM teacher_classes 
                                                     WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?");
                        $accessCheck->bind_param("iii", $teacherId, $classId, $armId);
                        $accessCheck->execute();
                        if ($accessCheck->get_result()->fetch_assoc()['count'] == 0) {
                            echo "<div class='alert alert-danger' role='alert'>Access denied to this class</div>";
                            exit;
                        }

                        // Update the base query to avoid duplicates
                        $baseQuery = "SELECT 
                            s.firstName, s.lastName, s.admissionNumber,
                            c.className, ca.classArmName,
                            DATE(a.dateTimeTaken) as attendanceDate,
                            MAX(CASE WHEN a.sessionNumber = 1 THEN a.status END) as session1,
                            MAX(CASE WHEN a.sessionNumber = 2 THEN a.status END) as session2
                        FROM tblstudents s
                        INNER JOIN tblattendance a ON s.admissionNumber = a.admissionNo
                        INNER JOIN tblclass c ON c.Id = s.classId
                        INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
                        WHERE s.admissionNumber = ?
                        AND s.classId = ?
                        AND s.classArmId = ?";

                        $params = array($admissionNumber, $classId, $armId);
                        $types = "sii";

                        if($type == "1") {
                            $query = $baseQuery . " GROUP BY attendanceDate ORDER BY attendanceDate DESC";
                        } else if($type == "2") {
                            $singleDate = $_POST['singleDate'];
                            if(empty($singleDate)) {
                                echo "<div class='alert alert-danger' role='alert'>Please select a date</div>";
                                exit;
                            }
                            $query = $baseQuery . " AND DATE(a.dateTimeTaken) = ? GROUP BY attendanceDate ORDER BY attendanceDate DESC";
                            $params[] = $singleDate;
                            $types .= "s";
                        } else if($type == "3") {
                            $fromDate = $_POST['fromDate'];
                            $toDate = $_POST['toDate'];
                            if(empty($fromDate) || empty($toDate)) {
                                echo "<div class='alert alert-danger' role='alert'>Please select both start and end dates</div>";
                                exit;
                            }
                            $query = $baseQuery . " AND DATE(a.dateTimeTaken) BETWEEN ? AND ? GROUP BY attendanceDate ORDER BY attendanceDate DESC";
                            $params[] = $fromDate;
                            $params[] = $toDate;
                            $types .= "ss";
                        }

                        $stmt = $conn->prepare($query);
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if($result->num_rows > 0) {
                            $sn = 0;
                            while ($rows = $result->fetch_assoc()) {
                                $sn++;
                                $statusDisplay = '';
                                
                                // Only show Session 1 if it exists
                                if (!is_null($rows['session1'])) {
                                    $statusClass = ($rows['session1'] == '1') ? 'badge badge-success' : 'badge badge-danger';
                                    $statusText = ($rows['session1'] == '1') ? 'Present' : 'Absent';
                                    $statusDisplay .= "<span class='{$statusClass}'>S1: {$statusText}</span> ";
                                }
                                
                                // Only show Session 2 if it exists
                                if (!is_null($rows['session2'])) {
                                    $statusClass = ($rows['session2'] == '1') ? 'badge badge-success' : 'badge badge-danger';
                                    $statusText = ($rows['session2'] == '1') ? 'Present' : 'Absent';
                                    $statusDisplay .= "<span class='{$statusClass}'>S2: {$statusText}</span> ";
                                }

                                echo "<tr>
                                    <td>".$sn."</td>
                                    <td>".htmlspecialchars($rows['firstName'])."</td>
                                    <td>".htmlspecialchars($rows['lastName'])."</td>
                                    <td>".htmlspecialchars($rows['admissionNumber'])."</td>
                                    <td>".htmlspecialchars($rows['className'])."</td>
                                    <td>".htmlspecialchars($rows['classArmName'])."</td>
                                    <td>".$statusDisplay."</td>
                                    <td>".$rows['attendanceDate']."</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'><div class='alert alert-info' role='alert'>
                            No attendance records found for this student
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
  </script>
</body>

</html>