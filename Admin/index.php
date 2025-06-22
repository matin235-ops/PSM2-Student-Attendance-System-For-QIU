<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';


// Use prepared statement for security
$stmt = $conn->prepare("SELECT tblclass.className, tblclassarms.classArmName 
                       FROM tblclassteacher
                       INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
                       INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
                       WHERE tblclassteacher.Id = ?");
$stmt->bind_param("s", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();
$rrw = $result->fetch_assoc();

// Optimize statistics queries into a single query
$statistics = [];
$tables = ['tblstudents', 'tblclass', 'tblclassarms', 'tblattendance', 
           'tblclassteacher', 'tblsessionterm', 'tblterm'];

foreach ($tables as $table) {
    $query = "SELECT COUNT(*) as count FROM " . $table;
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $statistics[$table] = $row['count'];
    } else {
        $statistics[$table] = 0; // Fallback if query fails
    }
}

// Get attendance statistics


// Get today's date
$today = date('Y-m-d');

// Calculate today's attendance statistics
$query = "SELECT 
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as absent
FROM tblattendance 
WHERE DATE(dateTimeTaken) = CURDATE()";

$result = $conn->query($query);
$row = $result->fetch_assoc();

$present = $row['present'] ?? 0;
$absent = $row['absent'] ?? 0;

// Add this after your existing queries
$attendanceQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as absent
    FROM tblattendance";

$attendanceResult = $conn->query($attendanceQuery);
$attendanceStats = $attendanceResult->fetch_assoc();

$present = $attendanceStats['present'] ?? 0;
$absent = $attendanceStats['absent'] ?? 0;

// Get class-wise attendance
$classQuery = "SELECT 
    c.className,
    COUNT(CASE WHEN a.status = '1' THEN 1 END) as present,
    COUNT(CASE WHEN a.status = '0' THEN 1 END) as absent
    FROM tblclass c
    LEFT JOIN tblattendance a ON a.classId = c.Id 
        AND DATE(a.dateTimeTaken) = ?
    GROUP BY c.className";

$stmt = $conn->prepare($classQuery);
$stmt->bind_param('s', $today);
$stmt->execute();
$classResult = $stmt->get_result();

// First, update the query at the top of the file where other queries exist
$attendanceQuery = "SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as absent_count
FROM tblattendance";

$attendanceResult = $conn->query($attendanceQuery);
$attendanceStats = $attendanceResult->fetch_assoc();

$totalRecords = $attendanceStats['total_records'];
$presentCount = $attendanceStats['present_count'];
$absentCount = $attendanceStats['absent_count'];

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
  <title>QIU Student Attendance </title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="css/custom.css" rel="stylesheet">
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
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
          </div>

          <!-- Content Row -->
          <div class="row">
              <!-- Total Students Card -->
              <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-primary shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <?php 
                                  $query1=mysqli_query($conn,"SELECT * from tblstudents");                       
                                  $students = mysqli_num_rows($query1);
                                  ?>
                                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $students;?></div>
                              </div>
                              <div class="col-auto">
                                  <i class="fas fa-users fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Total Teachers Card -->
              <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-success shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <?php 
                                  $query1=mysqli_query($conn,"SELECT * from tblclassteacher");                       
                                  $classTeacher = mysqli_num_rows($query1);
                                  ?>
                                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Teachers</div>
                                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classTeacher;?></div>
                              </div>
                              <div class="col-auto">
                                  <i class="fas fa-user-secret fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Total Classes Card -->
              <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-info shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <?php 
                                  $query1=mysqli_query($conn,"SELECT * from tblclass");                       
                                  $totalClass = mysqli_num_rows($query1);
                                  ?>
                                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Group</div>
                                  <div class="row no-gutters align-items-center">
                                      <div class="col-auto">
                                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $totalClass;?></div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-auto">
                                  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Total Sections Card -->
              <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-warning shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <?php 
                                  $query1=mysqli_query($conn,"SELECT * from tblclassarms");                       
                                  $totalClassArms = mysqli_num_rows($query1);
                                  ?>
                                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Subject</div>
                                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalClassArms;?></div>
                              </div>
                              <div class="col-auto">
                                  <i class="fas fa-comments fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          

          <!-- Put all these cards in the same row div -->
         

          <!-- Add this after the attendance/absence percentage cards -->
          

          <!-- Quick Action Buttons -->
          <div class="row mb-4">
              <div class="col-12">
                  <div class="card shadow">
                      <div class="card-header py-3">
                          <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <!-- Add Student Button -->
                              <div class="col-xl-3 col-md-6 mb-2">
                                  <a href="createStudents.php" class="btn btn-primary btn-icon-split btn-block">
                                      <span class="icon text-white-50">
                                          <i class="fas fa-user-plus"></i>
                                      </span>
                                      <span class="text">Add New Student</span>
                                  </a>
                              </div>

                              <!-- Add Teacher Button -->
                              <div class="col-xl-3 col-md-6 mb-2">
                                  <a href="createClassTeacher.php" class="btn btn-success btn-icon-split btn-block">
                                      <span class="icon text-white-50">
                                          <i class="fas fa-chalkboard-teacher"></i>
                                      </span>
                                      <span class="text">Add New Teacher</span>
                                  </a>
                              </div>

                              <!-- Manage Classes Button -->
                              <div class="col-xl-3 col-md-6 mb-2">
                                  <a href="createClass.php" class="btn btn-info btn-icon-split btn-block">
                                      <span class="icon text-white-50">
                                          <i class="fas fa-school"></i>
                                      </span>
                                      <span class="text">Manage Classes</span>
                                  </a>
                              </div>

                              <!-- Generate Report Button -->
                              <div class="col-xl-3 col-md-6 mb-2">
                                  <a href="#" class="btn btn-warning btn-icon-split btn-block" data-toggle="modal" data-target="#reportModal">
                                      <span class="icon text-white-50">
                                          <i class="fas fa-file-excel"></i>
                                      </span>
                                      <span class="text">Generate Report</span>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
  
        <!-- Report Modal -->
        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">Generate Attendance Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="reportForm" method="POST" action="generateExcel.php">
                            <div class="form-group">
                                <label>Select Report Type</label>
                                <select class="form-control" id="reportType" name="reportType">
                                    <option value="today">Today's Report</option>
                                    <option value="custom">Custom Date Range</option>
                                </select>
                            </div>
                            
                            <div id="customDateInputs" style="display: none;">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="fromDate" id="fromDate" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="toDate" id="toDate" class="form-control">
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include 'includes/footer.php';?>
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
  <script src="js/custom.js"></script> <!-- Custom animations and styles -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form elements
    const reportType = document.getElementById('reportType');
    const customDateInputs = document.getElementById('customDateInputs');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const reportForm = document.getElementById('reportForm');

    // Function to toggle date inputs
    function toggleDateInputs() {
        const isToday = reportType.value === 'today';
        customDateInputs.style.display = isToday ? 'none' : 'block';
        
        if (isToday) {
            const today = new Date().toISOString().split('T')[0];
            fromDate.value = today;
            toDate.value = today;
        } else {
            fromDate.value = '';
            toDate.value = '';
        }
    }

    // Initialize date inputs
    toggleDateInputs();

    // Add event listener for report type change
    reportType.addEventListener('change', toggleDateInputs);

    // Form validation and submission
    reportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (reportType.value === 'custom') {
            // Validate date inputs
            if (!fromDate.value || !toDate.value) {
                alert('Please select both From Date and To Date for custom range report');
                return false;
            }
            
            if (fromDate.value > toDate.value) {
                alert('From Date cannot be later than To Date');
                return false;
            }
        }

        // Create form data
        const formData = new FormData(this);
        
        // Submit form
        fetch('generateExcel.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Attendance_Report_${new Date().toISOString().split('T')[0]}.xls`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error generating report:', error);
            alert('Error generating report. Please try again.');
        });
    });

    // Initialize attendance chart if canvas exists
    const chartCanvas = document.getElementById('attendanceChart');
    if (chartCanvas) {
        const ctx = chartCanvas.getContext('2d');
        // Chart initialization code here...
    }
});
</script>
</body>
<style>
/* Color scheme variables */
:root {
    --primary: #003092;    /* Dark Blue */
    --secondary: #00879E;  /* Teal */
    --accent: #FFAB5B;     /* Orange */
    --light: #FFF2DB;      /* Light Cream */
    --white: #FFFFFF;
}

/* Update text colors */
.text-primary { color: var(--primary) !important; }
.text-info { color: var(--secondary) !important; }
.text-success { color: var(--secondary) !important; }
.text-warning { color: var(--accent) !important; }

/* Update button colors */
.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-success {
    background-color: var(--secondary);
    border-color: var(--secondary);
}

.btn-info {
    background-color: var(--accent);
    border-color: var(--accent);
}

.btn-warning {
    background-color: var(--light);
    border-color: var(--light);
    color: var(--primary);
}

/* Link colors */
.breadcrumb-item a {
    color: var(--primary);
}

/* Dashboard card styling */
.card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease-in-out;
    background-color: var(--white);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.card-body {
    padding: 1.5rem;
}

/* Quick action buttons */
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Breadcrumb styling */
.breadcrumb {
    background-color: transparent;
    padding: 0;
}

/* Animation for cards */
.animated-card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chart-area {
    position: relative;
    height: 300px;
    margin: 0 auto;
}

.chart-pie {
    position: relative;
    height: 250px;
    margin: 0 auto;
}

.btn-icon-split {
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.btn-icon-split:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-icon-split .icon {
    padding-right: 1rem;
    border-right: 1px solid rgba(255,255,255,0.2);
    margin-right: 1rem;
}

.btn-icon-split .text {
    display: inline-block;
    vertical-align: middle;
}
</style>
</html>
