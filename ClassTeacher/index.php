<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Add this after session check
if (!isset($_SESSION['userId'])) {
    header("Location: ../logout.php");
    exit();
}

// Add login activity log
$query = "INSERT INTO tblteacherlogs (teacherId, activity) VALUES ('$_SESSION[userId]', 'Accessed dashboard')";
$conn->query($query);

// Set timezone and get today's date
date_default_timezone_set('Asia/Baghdad');
$dateToday = date("Y-m-d");

// Replace the existing class and class arm name query with this improved version
$teacherId = $_SESSION['userId'];
$query = "SELECT c.className, ca.classArmName 
          FROM teacher_classes tc
          INNER JOIN tblclass c ON c.Id = tc.class_id
          INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
          WHERE tc.teacher_id = ?
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$result = $stmt->get_result();
$classInfo = $result->fetch_assoc();

// After the session check, add this code to set the class and arm IDs
if ($classInfo) {
    $_SESSION['className'] = $classInfo['className'];
    $_SESSION['classArmName'] = $classInfo['classArmName'];
}

// Replace the existing statistics queries with this improved version
$today = date('Y-m-d');

// Add this after the statistics query and before the HTML
// Check if attendance has been marked today for any class
$attendanceCheckQuery = "SELECT COUNT(*) as marked
                        FROM tblattendance a
                        INNER JOIN teacher_classes tc ON a.classId = tc.class_id 
                            AND a.classArmId = tc.class_arm_id
                        WHERE tc.teacher_id = ? 
                        AND DATE(a.dateTimeTaken) = ?";
$stmt = $conn->prepare($attendanceCheckQuery);
$stmt->bind_param("is", $teacherId, $today);
$stmt->execute();
$attendanceCheck = $stmt->get_result()->fetch_assoc();
$attendanceMarked = $attendanceCheck['marked'] > 0;

// Update the statistics query before the HTML
$statsQuery = "SELECT 
    COUNT(DISTINCT s.admissionNumber) as totalStudents,
    SUM(CASE WHEN a.dateTimeTaken IS NOT NULL THEN 1 ELSE 0 END) as totalAttendanceRecords,
    SUM(CASE WHEN a.status = '1' THEN 1 ELSE 0 END) as totalPresent,
    SUM(CASE WHEN a.status = '0' THEN 1 ELSE 0 END) as totalAbsent,
    SUM(CASE WHEN a.status = '1' AND a.sessionNumber = 1 THEN 1 ELSE 0 END) as presentSession1,
    SUM(CASE WHEN a.status = '0' AND a.sessionNumber = 1 THEN 1 ELSE 0 END) as absentSession1,
    SUM(CASE WHEN a.status = '1' AND a.sessionNumber = 2 THEN 1 ELSE 0 END) as presentSession2,
    SUM(CASE WHEN a.status = '0' AND a.sessionNumber = 2 THEN 1 ELSE 0 END) as absentSession2
FROM teacher_classes tc
INNER JOIN tblstudents s ON s.classId = tc.class_id AND s.classArmId = tc.class_arm_id
LEFT JOIN tblattendance a ON a.admissionNo = s.admissionNumber 
    AND DATE(a.dateTimeTaken) = ?
WHERE tc.teacher_id = ?";

$stmt = $conn->prepare($statsQuery);
$stmt->bind_param("si", $today, $teacherId);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Calculate totals with session-aware logic
$totalStudents = $stats['totalStudents'] ?? 0;
$totalPresent = $stats['totalPresent'] ?? 0;
$totalAbsent = $stats['totalAbsent'] ?? 0;
$expectedRecords = $totalStudents * 2; // 2 sessions per day

// Calculate attendance rate based on actual records
$attendanceRate = $expectedRecords > 0 ? round(($totalPresent / $expectedRecords) * 100, 1) : 0;

$presentSession1 = $stats['presentSession1'] ?? 0;
$absentSession1 = $stats['absentSession1'] ?? 0;
$presentSession2 = $stats['presentSession2'] ?? 0;
$absentSession2 = $stats['absentSession2'] ?? 0;

// Add warning class based on attendance rate
$rateClass = '';
if ($attendanceRate >= 90) {
    $rateClass = 'text-success';
} elseif ($attendanceRate >= 70) {
    $rateClass = 'text-warning';
} else {
    $rateClass = 'text-danger';
}

// Replace the existing message query with this
$messageQuery = "SELECT * FROM tblmessages 
                WHERE receiverId = '$teacherId' 
                AND receiverType = 'teacher'
                AND isRead = 0 
                ORDER BY created_at DESC";
$messageResult = $conn->query($messageQuery);

// Modify the class query to use teacher_classes table
$classQuery = "SELECT DISTINCT c.Id as classId, c.className, ca.Id as armId, ca.classArmName
               FROM teacher_classes tc
               INNER JOIN tblclass c ON c.Id = tc.class_id
               INNER JOIN tblclassarms ca ON ca.Id = tc.class_arm_id
               WHERE tc.teacher_id = ?
               ORDER BY c.className, ca.classArmName";

$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$classResult = $stmt->get_result();  // Store the result in $classResult

// Replace or remove the getAttendanceStatus function
function getAttendanceStatus($conn, $classId, $armId, $date) {
    // Always return false to keep the Take Attendance button active
    return false;
}

// First, get the first class assigned to the teacher
$firstClassQuery = "SELECT class_id, class_arm_id 
                   FROM teacher_classes 
                   WHERE teacher_id = ? 
                   LIMIT 1";
$stmt = $conn->prepare($firstClassQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$firstClass = $stmt->get_result()->fetch_assoc();
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
  <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
.stats-overview {
    background: transparent;
    padding: 15px 0;
    margin-bottom: 30px;
    box-shadow: none;
}

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: none;
}

.stats-card:hover {
    transform: none;
    border-color: #3498db;
}

/* Update Quick Actions styles */
.quick-actions {
    margin: 20px -10px;
}

.quick-actions .col-md-3 {
    padding: 0 10px;
}

.btn-action {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 56px;
    border-radius: 10px;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    width: 100%;
    margin-bottom: 15px;
    border: none;
    padding: 0 20px;
    position: relative;
    overflow: hidden;
}

.btn-action i {
    font-size: 18px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    position: relative;
    z-index: 1;
}

/* Updated button colors with gradients */
.btn-action.btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #3867d6 100%);
    color: white;
}

.btn-action.btn-info {
    background: linear-gradient(135deg, #36b9cc 0%, #2ea5c0 100%);
    color: white;
}

.btn-action.btn-success {
    background: linear-gradient(135deg, #1cc88a 0%, #16a085 100%);
    color: white;
}

.btn-action.btn-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #f39c12 100%);
    color: white;
}

/* Hover effects */
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-action::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0);
    transition: all 0.3s ease;
}

.btn-action:hover::after {
    background: rgba(255,255,255,0.1);
}

/* Stats card icon adjustments */
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.stats-icon i {
    font-size: 20px;
}

.stats-card:hover .stats-icon {
    transform: scale(1.1);
}

/* Class card button adjustments */
.btn-take-attendance {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    width: 100%;
}

.btn-take-attendance i {
    font-size: 14px;
    margin-right: 8px;
}

/* Section title update */
.section-title {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 2px solid #3498db;
    display: inline-block;
    background: transparent;
    padding: 0 0 5px 0;
    box-shadow: none;
}

/* Updated CSS classes */
.class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Reduced from 300px */
    gap: 15px; /* Reduced from 20px */
    margin-top: 20px;
}

.class-card {
    background: white;
    border-radius: 8px; /* Reduced from 12px */
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.class-header {
    padding: 12px 15px; /* Reduced padding */
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); /* New color scheme */
    color: white;
}

.class-name {
    font-size: 16px; /* Reduced from 18px */
    font-weight: 600;
    margin: 0;
}

.class-section {
    font-size: 12px; /* Reduced from 14px */
    opacity: 0.9;
    margin-top: 3px;
}

.class-body {
    padding: 15px; /* Reduced from 20px */
}

.attendance-status {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.status-indicator {
    width: 6px; /* Reduced from 8px */
    height: 6px; /* Reduced from 8px */
    border-radius: 50%;
    margin-right: 6px;
}

.status-text {
    font-size: 12px; /* Reduced from 14px */
    color: #6c757d;
}

.btn-take-attendance {
    padding: 8px 12px; /* Reduced padding */
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
}

/* Add new color variations for the attendance buttons */
.btn-take-attendance.btn-primary {
    background: #3498db;
    border-color: #3498db;
}

.btn-take-attendance.btn-primary:hover {
    background: #2980b9;
    border-color: #2980b9;
}

.btn-take-attendance.btn-info {
    background: #2ecc71;
    border-color: #2ecc71;
}

.btn-take-attendance.btn-info:hover {
    background: #27ae60;
    border-color: #27ae60;
}

/* Add a section title style */
.section-title {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #3498db;
    display: inline-block;
}

/* Add to your existing <style> section */
.quick-actions-vertical {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-action {
    text-align: left;
    padding: 15px;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.stats-card {
    border-radius: 10px;
    padding: 20px;
    background: white;
    transition: all 0.2s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f9 100%);
}

.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.class-card {
    margin-bottom: 0;
}

.dashboard-header h1 {
    font-weight: 700;
    color: #2c3e50;
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
            <!-- Dashboard Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Class Teacher Dashboard</h1>
                <?php if (!$attendanceMarked && $firstClass) { ?>
                    <a href="takeAttendance.php?class=<?php echo $firstClass['class_id']; ?>&arm=<?php echo $firstClass['class_arm_id']; ?>" 
                       class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-clipboard-list fa-sm text-white-50"></i> Take Today's Attendance
                    </a>
                <?php } ?>
            </div>

            <!-- Today Statistics Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Today's Overview</h6>
                            <div class="dropdown no-arrow">
                                <span class="text-xs text-gray-500"><?php echo date('F j, Y'); ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Total Students Card -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="stats-card h-100">
                                        <div class="stats-icon" style="background: rgba(78,115,223,0.1); color: #4e73df;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="stats-content">
                                            <div class="stats-label">Total Students</div>
                                            <h3><?php echo $totalStudents; ?></h3>
                                            <small class="text-muted">Enrolled</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Present Today Card -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="stats-card h-100">
                                        <div class="stats-icon" style="background: rgba(28,200,138,0.1); color: #1cc88a;">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div class="stats-content">
                                            <div class="stats-label">Present Today</div>
                                            <h3><?php echo $totalPresent; ?></h3>
                                            <small class="text-success">
                                                <?php echo $expectedRecords ? round(($totalPresent/$expectedRecords) * 100) : 0; ?>% attendance
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Absent Today Card -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="stats-card h-100">
                                        <div class="stats-icon" style="background: rgba(231,74,59,0.1); color: #e74a3b;">
                                            <i class="fas fa-user-times"></i>
                                        </div>
                                        <div class="stats-content">
                                            <div class="stats-label">Absent Today</div>
                                            <h3><?php echo $totalAbsent; ?></h3>
                                            <small class="text-danger">
                                                <?php echo $expectedRecords ? round(($totalAbsent/$expectedRecords) * 100) : 0; ?>% absence
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Overall Rate -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="stats-card h-100">
                                        <div class="stats-icon" style="background: rgba(246,194,62,0.1); color: #f6c23e;">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="stats-content">
                                            <div class="stats-label">Attendance Rate</div>
                                            <h3><?php echo $attendanceRate; ?>%</h3>
                                            <small class="<?php echo $attendanceRate >= 90 ? 'text-success' : ($attendanceRate >= 70 ? 'text-warning' : 'text-danger'); ?>">
                                                Overall performance
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and My Classes in Two Columns -->
            <div class="row">
                <!-- Quick Actions Column -->
                <div class="col-xl-4">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions-vertical">
                                <a href="takeAttendance.php" class="btn btn-action btn-primary mb-3">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>Take Attendance</span>
                                </a>
                                <a href="viewAttendance.php" class="btn btn-action btn-info mb-3">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>View Records</span>
                                </a>
                                <a href="viewStudentAttendance.php" class="btn btn-action btn-success mb-3">
                                    <i class="fas fa-user-check"></i>
                                    <span>Student Records</span>
                                </a>
                                <a href="viewAttendance.php#downloadReport" class="btn btn-action btn-warning mb-3">
                                    <i class="fas fa-download"></i>
                                    <span>Download Report</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Classes Column -->
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">My Classes</h6>
                        </div>
                        <div class="card-body">
                            <div class="class-grid">
                                <?php
                                if ($classResult && $classResult->num_rows > 0) {
                                    while ($row = $classResult->fetch_assoc()) {
                                        $isMarked = getAttendanceStatus($conn, $row['classId'], $row['armId'], date('Y-m-d'));
                                        ?>
                                        <div class="class-card">
                                            <div class="class-header">
                                                <h5 class="class-name"><?php echo htmlspecialchars($row['className']); ?></h5>
                                                <div class="class-section"><?php echo htmlspecialchars($row['classArmName']); ?></div>
                                            </div>
                                            <div class="class-body">
                                                <div class="attendance-status">
                                                    <span class="status-indicator" 
                                                          style="background: <?php echo $isMarked ? '#2ecc71' : '#e74c3c'; ?>">
                                                    </span>
                                                    <span class="status-text">
                                                        <?php echo $isMarked ? 'Marked' : 'Pending'; ?>
                                                    </span>
                                                </div>
                                                <div class="class-actions">
                                                    <a href="takeAttendance.php?class=<?php echo $row['classId']; ?>&arm=<?php echo $row['armId']; ?>" 
                                                       class="btn btn-take-attendance btn-primary">
                                                        <i class="fas fa-clipboard-check"></i>
                                                        Take Attendance
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No classes assigned yet
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>  
  <script src="js/messages.js"></script>
</body>

</html>