<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="../QIULOGO1.png" rel="icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analytics Dashboard</title>
    
    <!-- Include your existing CSS files -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Dashboard Styles -->
    <style>
        /* Enhance chart containers */
        .chart-area, .chart-pie, .chart-bar {
            position: relative;
            height: 350px;
            margin: 0 auto;
            padding: 15px;
            background: linear-gradient(to bottom right, #ffffff, #f8f9fa);
            border-radius: 12px;
        }

        /* Enhanced card styling */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-bottom: 2px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            border-radius: 15px 15px 0 0 !important;
        }

        .card-header h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            margin: 0;
            background: linear-gradient(45deg, #2c3e50, #3498db);
            -webkit-background-clip: text;                           
        }

        /* Chart loading animation */
        @keyframes chartFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-area, .chart-pie, .chart-bar {
            animation: chartFadeIn 0.8s ease-out forwards;
        }

        .progress {
            height: 0.5rem;
            border-radius: 1rem;
            background-color: rgba(0,0,0,0.05);
        }

        .progress-bar {
            border-radius: 1rem;
            transition: width 1s ease;
        }        @media (max-width: 768px) {
            .chart-area, .chart-pie, .chart-bar {
                height: 300px;
            }
        }        /* Session Filter Dropdown Styles */
        .dropdown-item.active {
            background-color: #4e73df;
            color: white;
        }

        .dropdown-item.active:hover {
            background-color: #2e59d9;
            color: white;
        }

        .dropdown-item:hover {
            background-color: #f8f9fc;
        }

        /* Apply same styles to subject filter */
        .subject-filter-option.active {
            background-color: #4e73df;
            color: white;
        }

        .subject-filter-option.active:hover {
            background-color: #2e59d9;
            color: white;
        }

        /* Loading spinner styles */
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        #sessionChartLoading {
            padding: 3rem 0;
            color: #5a5c69;
        }

        #sessionNoData {
            padding: 3rem 0;
        }

        /* Enhanced chart responsiveness */
        .chart-bar canvas {
            max-height: 400px;
        }

        /* Filter button styling */
        .card-header .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }        .card-header .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        /* Additional styles for better UX */
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .chart-container {
            position: relative;
            min-height: 350px;
        }

        /* Smooth transitions for chart updates */
        .chart-bar {
            transition: opacity 0.3s ease;
        }

        .chart-bar.loading {
            opacity: 0.5;
        }

        /* Responsive improvements */
        @media (max-width: 576px) {
            .card-header {
                flex-direction: column;
                text-align: center;
            }
            
            .card-header .dropdown {
                margin-top: 10px;
            }
            
            .dropdown-menu {
                position: static !important;
                width: 100%;
                border: none;
                box-shadow: none;
                background-color: #f8f9fc;
                border-radius: 8px;
                margin-top: 10px;
            }
        }

        .card h6 {
            color: #1a75ff;
            margin: 0 0 10px 0;
            font-size: 1rem;
            font-weight: bold;
        }

        .card .icon {
            position: absolute;
            right: 20px;
            top: 20px;
            color: rgba(26, 117, 255, 0.15);
            font-size: 2.5rem;
        }

        .card .text-xs {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #858796;
            margin-bottom: 0.5rem;
        }

        .card .h5 {
            font-size: 1.5rem;
            color: #5a5c69;
            margin-bottom: 0.25rem;
        }

        .card .text-muted {
            color: #858796 !important;
            font-size: 0.8rem;
        }

        /* Card hover effects and animations */
        .card {
            transition: all 0.3s ease;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(0,0,0,.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Icon styles for different cards */
        .card .fa-user-check {
            color: #36b9cc;
        }

        .card .fa-user-times {
            color: #e74a3b;
        }

        .card .fa-chalkboard {
            color: #1cc88a;
        }

        .card .fa-calendar-check {
            color: #f6c23e;
        }

        /* Progress bar styles if needed */
        .progress-sm {
            height: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card .h5 {
                font-size: 1.25rem;
            }
            
            .card .icon {
                font-size: 2rem;
            }
        }

        /* Card color variations */
        .card.bg-primary {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: #fff;
        }

        .card.bg-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
            color: #fff;
        }

        .card.bg-info {
            background: linear-gradient(45deg, #36b9cc, #258391);
            color: #fff;
        }

        .card.bg-warning {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
            color: #fff;
        }

        /* Text colors for gradient backgrounds */
        .card[class*="bg-"] .text-xs,
        .card[class*="bg-"] .h5,
        .card[class*="bg-"] .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .card[class*="bg-"] .icon {
            color: rgba(255, 255, 255, 0.2);
        }

        /* Animation for numbers */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card .h5 {
            animation: countUp 0.6s ease-out forwards;
        }

        /* Add these styles in your <style> section */
        .stat-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.2));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card.primary-card {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }

        .stat-card.danger-card {
            background: linear-gradient(45deg, #e74a3b, #be2617);
        }

        .stat-card.success-card {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }

        .stat-card.info-card {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }

        .stat-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-card .text-xs {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.75rem;
        }

        .stat-card .h5 {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-card .icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover .icon {
            transform: translateY(-50%) scale(1.1);
            color: rgba(255, 255, 255, 0.3);
        }

        .stat-card .progress {
            height: 0.35rem;
            background-color: rgba(255, 255, 255, 0.2);
            margin-top: 1rem;
            border-radius: 1rem;
        }

        .stat-card .progress-bar {
            border-radius: 1rem;
            background-color: rgba(255, 255, 255, 0.8);
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

                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
                    </div>

                    <!-- Stats Cards Row -->
                    <div class="row mb-4">
                        <!-- Present Students Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card primary-card h-100">
                                <div class="card-body">
                                    <?php
                                    // Get today's attendance data
                                    $todayQuery = "SELECT 
                                        COUNT(*) as total, 
                                        SUM(status = 1) as present 
                                        FROM tblattendance 
                                        WHERE DATE(dateTimeTaken) = CURDATE()";
                                    $todayResult = $conn->query($todayQuery);
                                    $todayData = $todayResult->fetch_assoc();
                                    $presentPercentage = ($todayData['total'] > 0) ? 
                                        ($todayData['present'] / $todayData['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Today's Present</div>
                                    <div class="h5"><?php echo number_format($presentPercentage, 1); ?>%</div>
                                    <div class="text-muted">
                                        Present: <?php echo $todayData['present'] ?? 0; ?> of <?php echo $todayData['total'] ?? 0; ?> today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $presentPercentage; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Absent Students Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card danger-card h-100">
                                <div class="card-body">
                                    <?php
                                    $absentCount = $todayData['total'] - $todayData['present'];
                                    $absentPercentage = ($todayData['total'] > 0) ? 
                                        ($absentCount / $todayData['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Today's Absent</div>
                                    <div class="h5"><?php echo number_format($absentPercentage, 1); ?>%</div>
                                    <div class="text-muted">
                                        Absent: <?php echo $absentCount; ?> students today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $absentPercentage; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Classes Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card success-card h-100">
                                <div class="card-body">
                                    <?php
                                    $classQuery = "SELECT COUNT(DISTINCT classId) as totalClasses 
                                                  FROM tblattendance 
                                                  WHERE DATE(dateTimeTaken) = CURDATE()";
                                    $classResult = $conn->query($classQuery);
                                    $classCount = $classResult->fetch_assoc()['totalClasses'];
                                    ?>
                                    <div class="text-xs">Today's Active Classes</div>
                                    <div class="h5"><?php echo $classCount; ?></div>
                                    <div class="text-muted">
                                        Classes with attendance today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chalkboard"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Coverage Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card info-card h-100">
                                <div class="card-body">
                                    <?php
                                    $subjectQuery = "SELECT COUNT(DISTINCT classArmId) as coveredSubjects 
                                                    FROM tblattendance 
                                                    WHERE DATE(dateTimeTaken) = CURDATE()";
                                    $subjectResult = $conn->query($subjectQuery);
                                    $coveredSubjects = $subjectResult->fetch_assoc()['coveredSubjects'];
                                    
                                    $totalSubjectsQuery = "SELECT COUNT(*) as total FROM tblclassarms";
                                    $totalSubjectsResult = $conn->query($totalSubjectsQuery);
                                    $totalSubjects = $totalSubjectsResult->fetch_assoc()['total'];
                                    
                                    $coveragePercentage = ($totalSubjects > 0) ? 
                                        ($coveredSubjects / $totalSubjects * 100) : 0;
                                    ?>
                                    <div class="text-xs">Today's Subject Coverage</div>
                                    <div class="h5"><?php echo number_format($coveragePercentage, 1); ?>%</div>
                                    <div class="text-muted">
                                        <?php echo $coveredSubjects; ?> of <?php echo $totalSubjects; ?> subjects covered
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $coveragePercentage; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session 1 Present Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card primary-card h-100">
                                <div class="card-body">
                                    <?php
                                    // Get today's session 1 attendance data
                                    $session1Query = "SELECT 
                                        COUNT(*) as total, 
                                        SUM(status = 1) as present 
                                        FROM tblattendance 
                                        WHERE DATE(dateTimeTaken) = CURDATE() 
                                        AND sessionNumber = 1";
                                    $session1Result = $conn->query($session1Query);
                                    $session1Data = $session1Result->fetch_assoc();
                                    $presentPercentage1 = ($session1Data['total'] > 0) ? 
                                        ($session1Data['present'] / $session1Data['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Session 1 Attendance</div>
                                    <div class="h5"><?php echo number_format($presentPercentage1, 1); ?>%</div>
                                    <div class="text-muted">
                                        Present: <?php echo $session1Data['present'] ?? 0; ?> of <?php echo $session1Data['total'] ?? 0; ?> today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $presentPercentage1; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session 1 Absent Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card danger-card h-100">
                                <div class="card-body">
                                    <?php
                                    // Calculate session 1 absent data from existing query results
                                    $absentCount1 = $session1Data['total'] - $session1Data['present'];
                                    $absentPercentage1 = ($session1Data['total'] > 0) ? 
                                        ($absentCount1 / $session1Data['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Session 1 Absent</div>
                                    <div class="h5"><?php echo number_format($absentPercentage1, 1); ?>%</div>
                                    <div class="text-muted">
                                        Absent: <?php echo $absentCount1; ?> of <?php echo $session1Data['total'] ?? 0; ?> today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $absentPercentage1; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session 2 Present Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card success-card h-100">
                                <div class="card-body">
                                    <?php
                                    // Get today's session 2 attendance data
                                    $session2Query = "SELECT 
                                        COUNT(*) as total, 
                                        SUM(status = 1) as present 
                                        FROM tblattendance 
                                        WHERE DATE(dateTimeTaken) = CURDATE() 
                                        AND sessionNumber = 2";
                                    $session2Result = $conn->query($session2Query);
                                    $session2Data = $session2Result->fetch_assoc();
                                    $presentPercentage2 = ($session2Data['total'] > 0) ? 
                                        ($session2Data['present'] / $session2Data['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Session 2 Attendance</div>
                                    <div class="h5"><?php echo number_format($presentPercentage2, 1); ?>%</div>
                                    <div class="text-muted">
                                        Present: <?php echo $session2Data['present'] ?? 0; ?> of <?php echo $session2Data['total'] ?? 0; ?> today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $presentPercentage2; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session 2 Absent Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stat-card danger-card h-100">
                                <div class="card-body">
                                    <?php
                                    // Calculate session 2 absent data from existing query results
                                    $absentCount2 = $session2Data['total'] - $session2Data['present'];
                                    $absentPercentage2 = ($session2Data['total'] > 0) ? 
                                        ($absentCount2 / $session2Data['total'] * 100) : 0;
                                    ?>
                                    <div class="text-xs">Session 2 Absent</div>
                                    <div class="h5"><?php echo number_format($absentPercentage2, 1); ?>%</div>
                                    <div class="text-muted">
                                        Absent: <?php echo $absentCount2; ?> of <?php echo $session2Data['total'] ?? 0; ?> today
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $absentPercentage2; ?>%"></div>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        
                        <!-- Daily Attendance Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Daily Attendance Overview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="dailyAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Attendance Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Monthly Attendance Distribution</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie">
                                        <canvas id="monthlyAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Class-wise Attendance -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Class-wise Attendance Rate</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="classAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Subject-wise Attendance Chart with Filter -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Subject-wise Attendance Rate</h6>
                                    <div class="dropdown no-arrow">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="subjectFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-filter fa-sm"></i> Filter
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="subjectFilterDropdown">
                                            <div class="dropdown-header">Time Period:</div>
                                            <a class="dropdown-item subject-filter-option" href="#" data-filter="today">
                                                <i class="fas fa-calendar-day fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Today
                                            </a>
                                            <a class="dropdown-item subject-filter-option" href="#" data-filter="week">
                                                <i class="fas fa-calendar-week fa-sm fa-fw mr-2 text-gray-400"></i>
                                                This Week
                                            </a>
                                            <a class="dropdown-item subject-filter-option" href="#" data-filter="month">
                                                <i class="fas fa-calendar-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                This Month
                                            </a>
                                            <a class="dropdown-item subject-filter-option active" href="#" data-filter="all">
                                                <i class="fas fa-calendar fa-sm fa-fw mr-2 text-gray-400"></i>
                                                All Time
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="subjectChartLoading" class="text-center" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Updating chart...</p>
                                    </div>
                                    <div class="chart-bar">
                                        <canvas id="subjectAttendanceChart"></canvas>
                                    </div>                                    <div id="subjectNoData" class="text-center" style="display: none;">
                                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">No attendance data available for the selected period</p>
                                    </div>
                                    <div id="subjectDebugInfo" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <h6><strong>Debug Information:</strong></h6>
                                            <div id="subjectDebugContent"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- Session Comparison Chart -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Session Attendance Comparison</h6>
                                    <div class="dropdown no-arrow">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="sessionFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-filter fa-sm"></i> Filter
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="sessionFilterDropdown">
                                            <div class="dropdown-header">Time Period:</div>
                                            <a class="dropdown-item session-filter-option" href="#" data-filter="today">
                                                <i class="fas fa-calendar-day fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Today
                                            </a>
                                            <a class="dropdown-item session-filter-option" href="#" data-filter="week">
                                                <i class="fas fa-calendar-week fa-sm fa-fw mr-2 text-gray-400"></i>
                                                This Week
                                            </a>
                                            <a class="dropdown-item session-filter-option" href="#" data-filter="month">
                                                <i class="fas fa-calendar-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                This Month
                                            </a>
                                            <a class="dropdown-item session-filter-option active" href="#" data-filter="all">
                                                <i class="fas fa-calendar fa-sm fa-fw mr-2 text-gray-400"></i>
                                                All Time
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="sessionChartLoading" class="text-center" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Updating chart...</p>
                                    </div>
                                    <div class="chart-bar">
                                        <canvas id="sessionComparisonChart"></canvas>
                                    </div>
                                    <div id="sessionNoData" class="text-center" style="display: none;">
                                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">No attendance data available for the selected period</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Include your existing JS files -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.register(ChartDataLabels); // Register the datalabels plugin
        
        // Common chart options
        const commonOptions = {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Poppins',
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        padding: 25,
                        color: '#2c3e50'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#2c3e50',
                    bodyColor: '#5a6c7d',
                    titleFont: {
                        size: 14,
                        weight: '600',
                        family: 'Poppins'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Poppins'
                    },
                    displayColors: true,
                    padding: 15,
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                },
                datalabels: {
                    color: '#2c3e50',
                    font: {
                        family: 'Poppins',
                        weight: '600',
                        size: 12
                    },
                    padding: 8,
                    textStrokeColor: '#fff',
                    textStrokeWidth: 2,
                    textShadowBlur: 5,
                    textShadowColor: 'rgba(0,0,0,0.2)'
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart',
                delay: (context) => context.dataIndex * 100
            },
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 20,
                    right: 25,
                    bottom: 20,
                    left: 25
                }
            }
        };        // Fetch data for daily attendance
        <?php
        // Update the query for the daily attendance chart to include session information with proper handling
        $query = "SELECT 
            DATE(dateTimeTaken) as date,
            SUM(CASE WHEN status = 1 AND sessionNumber = 1 THEN 1 ELSE 0 END) as present_s1,
            SUM(CASE WHEN status = 0 AND sessionNumber = 1 THEN 1 ELSE 0 END) as absent_s1,
            SUM(CASE WHEN status = 1 AND sessionNumber = 2 THEN 1 ELSE 0 END) as present_s2,
            SUM(CASE WHEN status = 0 AND sessionNumber = 2 THEN 1 ELSE 0 END) as absent_s2
            FROM tblattendance
            WHERE dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            AND sessionNumber IS NOT NULL 
            AND sessionNumber != ''
            GROUP BY DATE(dateTimeTaken)
            ORDER BY date DESC
            LIMIT 7";

        $result = $conn->query($query);
        $dates = [];
        $presentDataS1 = [];
        $absentDataS1 = [];
        $presentDataS2 = [];
        $absentDataS2 = [];

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $dates[] = date('M d', strtotime($row['date']));
                $presentDataS1[] = intval($row['present_s1']);
                $absentDataS1[] = intval($row['absent_s1']);
                $presentDataS2[] = intval($row['present_s2']);
                $absentDataS2[] = intval($row['absent_s2']);
            }
        }
        ?>

        // Daily Attendance Chart
        var dailyCtx = document.getElementById('dailyAttendanceChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Session 1 Present',
                    data: <?php echo json_encode($presentDataS1); ?>,
                    borderColor: 'rgba(52, 152, 219, 1)',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Session 2 Present',
                    data: <?php echo json_encode($presentDataS2); ?>,
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.2)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0 // Force whole numbers
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y || 0;
                                return `${label}: ${value} students`;
                            }
                        }
                    },
                    datalabels: {
                        align: 'top',
                        formatter: value => Math.round(value),
                        color: '#333',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        });        // Monthly Attendance Chart
        <?php
        $monthlyQuery = "SELECT 
            MONTH(dateTimeTaken) as month,
            COUNT(*) as total,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as present
            FROM tblattendance
            WHERE dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            AND sessionNumber IS NOT NULL 
            AND sessionNumber != ''
            GROUP BY MONTH(dateTimeTaken)";
        
        $monthlyResult = $conn->query($monthlyQuery);
        $monthlyData = [];
        $totalAttendance = 0;
        $totalAbsent = 0;

        if($monthlyResult && $monthlyResult->num_rows > 0) {
            while($row = $monthlyResult->fetch_assoc()) {
                $monthlyData[] = $row['present'];
                $totalAttendance += $row['present'];
                $totalAbsent += ($row['total'] - $row['present']);
            }
        }
        ?>

        function showNoDataMessage(chartId) {
            const ctx = document.getElementById(chartId).getContext('2d');
            ctx.font = '16px Poppins';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#666';
            ctx.fillText('No attendance data available', ctx.canvas.width / 2, ctx.canvas.height / 2);
        }

        if (<?php echo $totalAttendance; ?> === 0) {
            showNoDataMessage('monthlyAttendanceChart');
        } else {
            new Chart(document.getElementById('monthlyAttendanceChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{                        data: [
                            <?php echo $totalAttendance; ?>,
                            <?php echo $totalAbsent; ?>
                        ],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.9)',    // Blue for present
                            'rgba(252, 84, 75, 0.9)'      // Red for absent
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverBackgroundColor: [
                            'rgba(52, 152, 219, 1)',
                            'rgba(252, 84, 75, 1)'
                        ],
                        hoverBorderWidth: 4,
                        hoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '70%',
                    rotation: -90,
                    circumference: 360,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 13,
                                    family: 'Poppins',
                                    weight: '600'
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.map(data => { sum += data });
                                let percentage = (value * 100 / sum).toFixed(1) + "%";
                                return percentage;
                            },
                            color: '#fff',
                            font: {
                                size: 16,
                                weight: 'bold',
                                family: 'Poppins'
                            },
                            textShadow: '0 1px 2px rgba(0,0,0,0.2)'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#333',
                            titleFont: {
                                size: 13,
                                weight: '600',
                                family: 'Poppins'
                            },
                            bodyColor: '#666',
                            bodyFont: {
                                size: 12,
                                family: 'Poppins'
                            },
                            borderColor: 'rgba(0,0,0,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }        // Class-wise Attendance Chart
        <?php
        $classQuery = "SELECT 
            c.className,
            SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
            COUNT(a.id) as total,
            SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 1 THEN 1 ELSE 0 END) as present_s1,
            SUM(CASE WHEN a.sessionNumber = 1 THEN 1 ELSE 0 END) as total_s1,
            SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 1 THEN 1 ELSE 0 END) as present_s2,
            SUM(CASE WHEN a.sessionNumber = 2 THEN 1 ELSE 0 END) as total_s2
            FROM tblclass c
            LEFT JOIN tblattendance a ON c.Id = a.classId AND (a.sessionNumber IS NOT NULL AND a.sessionNumber != '')
            GROUP BY c.className";
        
        $classResult = $conn->query($classQuery);
        $classes = [];
        $classData = [];

        if($classResult && $classResult->num_rows > 0) {
            while($row = $classResult->fetch_assoc()) {
                $classes[] = $row['className'];
                $classData[] = $row['total'] > 0 ? ($row['present'] / $row['total']) * 100 : 0;
            }
        } else {
            $classes = ['No Data'];
            $classData = [0];
        }
        ?>

        if (<?php echo json_encode($classes); ?>[0] === 'No Data') {
            showNoDataMessage('classAttendanceChart');
        } else {
            new Chart(document.getElementById('classAttendanceChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($classes); ?>,
                    datasets: [{
                        label: 'Attendance Rate',
                        data: <?php echo json_encode($classData); ?>,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.5,
                        categoryPercentage: 0.8,
                        // Update gradient in Class-wise Attendance Chart
                        backgroundImage: 'linear-gradient(180deg, rgba(52, 152, 219, 0.9) 0%, rgba(252, 84, 75, 0.9) 100%)'
                    }]
                },
                options: {
                    ...commonOptions,
                    maintainAspectRatio: false,  // Added to ensure proper height
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,  // Added to rotate labels
                                minRotation: 45,  // Added to rotate labels
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: value => value.toFixed(1) + '%',
                            offset: 4  // Added to adjust label position
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,  // Added padding to prevent label cutoff
                            bottom: 10
                        }
                    }
                }
            });
        }        // Subject-wise Attendance Chart
        let subjectChart = null;        function loadSubjectAttendanceData(filter = 'all') {
            // Show loading spinner
            document.getElementById('subjectChartLoading').style.display = 'block';
            document.getElementById('subjectAttendanceChart').style.display = 'none';
            document.getElementById('subjectNoData').style.display = 'none';

            // Make AJAX request
            fetch('get_subject_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ filter: filter })            })
            .then(response => response.json())
            .then(data => {                // Debug: Log the entire response to console
                console.log('Subject Data Response:', data);
                if (data.debug) {
                    console.log('Debug Info:', data.debug);
                    // Display debug info on page
                    const debugDiv = document.getElementById('subjectDebugInfo');
                    const debugContent = document.getElementById('subjectDebugContent');
                    debugContent.innerHTML = `
                        <p><strong>Filter:</strong> ${data.debug.filter}</p>
                        <p><strong>Date Condition:</strong> ${data.debug.dateCondition || 'None (all time)'}</p>
                        <p><strong>Check Data Query:</strong> <code>${data.debug.checkDataQuery}</code></p>
                        <p><strong>Check Data Count:</strong> ${data.debug.checkDataCount}</p>
                        <p><strong>Subject Query:</strong> <code>${data.debug.subjectQuery}</code></p>
                        <p><strong>Subject Rows:</strong> ${data.debug.subjectRows}</p>
                        <p><strong>DB Errors:</strong> ${data.debug.dbErrors.length > 0 ? data.debug.dbErrors.join(', ') : 'None'}</p>
                    `;
                    debugDiv.style.display = 'block';
                } else {
                    document.getElementById('subjectDebugInfo').style.display = 'none';
                }
                
                // Hide loading spinner
                document.getElementById('subjectChartLoading').style.display = 'none';
                document.getElementById('subjectAttendanceChart').style.display = 'block';

                if (data.error) {
                    console.error('Error:', data.error);
                    document.getElementById('subjectNoData').style.display = 'block';
                    document.getElementById('subjectAttendanceChart').style.display = 'none';
                    return;
                }                if (data.classSubjects.length === 0) {
                    document.getElementById('subjectNoData').style.display = 'block';
                    document.getElementById('subjectAttendanceChart').style.display = 'none';
                    return;
                }

                // Hide no data message and debug info when we have data
                document.getElementById('subjectNoData').style.display = 'none';
                document.getElementById('subjectDebugInfo').style.display = 'none';

                // Destroy existing chart if it exists
                if (subjectChart) {
                    subjectChart.destroy();
                }

                // Create new chart
                const ctx = document.getElementById('subjectAttendanceChart').getContext('2d');
                subjectChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.classSubjects,
                        datasets: [{
                            label: 'Attendance Rate by Subject',
                            data: data.subjectData,
                            backgroundColor: data.backgroundColors,
                            borderColor: data.backgroundColors,
                            borderWidth: 2,
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        }]
                    },
                    options: {
                        ...commonOptions,
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
                                    font: {
                                        size: 12,
                                        family: 'Poppins'
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Attendance Rate (%)',
                                    font: {
                                        size: 14,
                                        weight: '600',
                                        family: 'Poppins'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 11,
                                        family: 'Poppins'
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Class - Subject',
                                    font: {
                                        size: 14,
                                        weight: '600',
                                        family: 'Poppins'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: 'rgba(0,0,0,0.1)',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return `Attendance Rate: ${value.toFixed(1)}%`;
                                    },
                                    afterLabel: function(context) {
                                        const subjectIndex = context.dataIndex;
                                        const presentCount = data.presentCounts[subjectIndex];
                                        const totalCount = data.totalCounts[subjectIndex];
                                        return `Present: ${presentCount}/${totalCount} students`;
                                    }
                                }
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                formatter: value => value.toFixed(1) + '%',
                                color: '#333',
                                font: {
                                    weight: 'bold',
                                    family: 'Poppins',
                                    size: 11
                                },
                                offset: 4
                            }
                        },
                        layout: {
                            padding: {
                                top: 25,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching subject data:', error);
                document.getElementById('subjectChartLoading').style.display = 'none';
                document.getElementById('subjectNoData').style.display = 'block';
                document.getElementById('subjectAttendanceChart').style.display = 'none';
            });
        }

        // Add event listeners for subject filter dropdown
        document.querySelectorAll('.subject-filter-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active state
                document.querySelectorAll('.subject-filter-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                
                // Get filter value and load data
                const filter = this.getAttribute('data-filter');
                loadSubjectAttendanceData(filter);
            });
        });

        // Load initial subject attendance data
        loadSubjectAttendanceData('all');        <?php
        // Remove the old subject attendance code - it's now handled by AJAX
        ?>

        // Session Comparison Chart
        let sessionChart = null;
        
        function loadSessionComparisonData(filter = 'all') {
            // Show loading spinner
            document.getElementById('sessionChartLoading').style.display = 'block';
            document.getElementById('sessionComparisonChart').style.display = 'none';
            document.getElementById('sessionNoData').style.display = 'none';

            // Make AJAX request
            fetch('get_session_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ filter: filter })
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('sessionChartLoading').style.display = 'none';
                document.getElementById('sessionComparisonChart').style.display = 'block';

                if (data.error) {
                    console.error('Error:', data.error);
                    document.getElementById('sessionNoData').style.display = 'block';
                    document.getElementById('sessionComparisonChart').style.display = 'none';
                    return;
                }

                if (data.classNames.length === 0) {
                    document.getElementById('sessionNoData').style.display = 'block';
                    document.getElementById('sessionComparisonChart').style.display = 'none';
                    return;
                }

                // Destroy existing chart if it exists
                if (sessionChart) {
                    sessionChart.destroy();
                }

                // Create new chart
                const ctx = document.getElementById('sessionComparisonChart').getContext('2d');
                sessionChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.classNames,
                        datasets: [
                            {
                                label: 'Session 1 Attendance Rate',
                                data: data.session1Rate,
                                backgroundColor: 'rgba(52, 152, 219, 0.8)',
                                borderColor: 'rgba(52, 152, 219, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Session 2 Attendance Rate',
                                data: data.session2Rate,
                                backgroundColor: 'rgba(46, 204, 113, 0.8)',
                                borderColor: 'rgba(46, 204, 113, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        ...commonOptions,
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
                                    font: {
                                        size: 12,
                                        family: 'Poppins'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 12,
                                        family: 'Poppins'
                                    }
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Poppins',
                                        size: 13,
                                        weight: '600'
                                    },
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: 'rgba(0,0,0,0.1)',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return `${context.dataset.label}: ${value.toFixed(1)}%`;
                                    },
                                    afterLabel: function(context) {
                                        const classIndex = context.dataIndex;
                                        const sessionNum = context.datasetIndex + 1;
                                        const presentCount = sessionNum === 1 ? data.session1Present[classIndex] : data.session2Present[classIndex];
                                        const totalCount = sessionNum === 1 ? data.session1Total[classIndex] : data.session2Total[classIndex];
                                        return `Present: ${presentCount}/${totalCount} students`;
                                    }
                                }
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                formatter: value => value.toFixed(1) + '%',
                                color: '#333',
                                font: {
                                    weight: 'bold',
                                    family: 'Poppins',
                                    size: 11
                                },
                                offset: 4
                            }
                        },
                        layout: {
                            padding: {
                                top: 25,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching session data:', error);
                document.getElementById('sessionChartLoading').style.display = 'none';
                document.getElementById('sessionNoData').style.display = 'block';
                document.getElementById('sessionComparisonChart').style.display = 'none';
            });
        }

        // Add event listeners for filter dropdown
        document.querySelectorAll('.session-filter-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active state
                document.querySelectorAll('.session-filter-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                
                // Get filter value and load data
                const filter = this.getAttribute('data-filter');
                loadSessionComparisonData(filter);
            });
        });

        // Load initial session comparison data
        loadSessionComparisonData('all');

        <?php
        // Remove the old session comparison code - it's now handled by AJAX
        ?>
    });
    </script>
</body>
</html>