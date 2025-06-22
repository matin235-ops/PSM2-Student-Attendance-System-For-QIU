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
            font-family: 'Poppins', sans-serif;            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            margin: 0;
            background: linear-gradient(45deg, #2c3e50, #3498db);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
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
        }

        @media (max-width: 768px) {
            .chart-area, .chart-pie, .chart-bar {
                height: 300px;
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
        }        .stat-card .progress-bar {
            border-radius: 1rem;
            background-color: rgba(255, 255, 255, 0.8);
        }        /* Custom Tooltip Styles */
        #chartjs-tooltip {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            font-family: 'Poppins', sans-serif;
            max-width: 320px;
            max-height: 400px;
            overflow: hidden;
            transition: opacity 0.2s ease;
            cursor: default;
        }

        .tooltip-header {
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 8px 8px 0 0;
            position: relative;
        }

        .tooltip-close {
            position: absolute;
            top: 8px;
            right: 10px;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            font-size: 16px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .tooltip-close:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .tooltip-scroll-content {
            max-height: 340px;
            overflow-y: auto;
            padding: 0;
        }

        .tooltip-scroll-content::-webkit-scrollbar {
            width: 6px;
        }

        .tooltip-scroll-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .tooltip-scroll-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .tooltip-scroll-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .tooltip-section {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .tooltip-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .tooltip-item {
            color: #34495e;
            margin-bottom: 4px;
            font-size: 12px;
            line-height: 1.4;
        }        .tooltip-item:last-child {
            margin-bottom: 0;
        }

        /* Absenteeism Table Styles */
        #absenteeismTable {
            font-size: 0.875rem;
        }

        #absenteeismTable th {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px 8px;
            border: none;
        }

        #absenteeismTable td {
            text-align: center;
            padding: 10px 8px;
            vertical-align: middle;
        }

        #absenteeismTable tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-critical {
            background-color: #dc3545;
            color: white;
        }

        .status-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .status-good {
            background-color: #28a745;
            color: white;
        }

        .attendance-rate {
            font-weight: 600;
        }

        .rate-low {
            color: #dc3545;
        }

        .rate-medium {
            color: #ffc107;
        }

        .rate-high {
            color: #28a745;
        }

        .absent-count {
            font-weight: 600;
            color: #dc3545;
        }        .present-count {
            font-weight: 600;
            color: #28a745;
        }

        /* Print Styles */
        @media print {
            .btn, .no-print { display: none !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
            .card-header { background-color: #f8f9fa !important; color: #000 !important; }
            body { font-size: 12px; }
        }

        /* Report Generation Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

                <div class="container-fluid" id="container-wrapper">                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
                    <div class="d-flex">
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2" onclick="generateAnalyticsReport()">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Full Analytics Report
                        </a>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="printAnalytics()">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print Analytics
                        </a>
                    </div>
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
                    </div>                    <!-- Add this HTML after the Class-wise Attendance div -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Subject-wise Attendance Rate</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="subjectAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- High Absenteeism Analysis Table -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-danger">High Absenteeism Analysis</h6>                    <div class="d-flex align-items-center">
                        <label for="timeFilter" class="mr-2 mb-0 font-weight-bold">Filter by:</label>
                        <select id="timeFilter" class="form-control form-control-sm mr-3" style="width: 150px;" onchange="filterAbsenteeismData()">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="all" selected>All Time</option>
                        </select>
                        <button type="button" class="btn btn-success btn-sm" onclick="generateAbsenteeismReport()">
                            <i class="fas fa-file-excel mr-1"></i>Generate Report
                        </button>
                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="absenteeismTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Class</th>
                                                    <th>Subject</th>
                                                    <th>Total Records</th>
                                                    <th>Present</th>
                                                    <th>Absent</th>
                                                    <th>Attendance Rate</th>
                                                    <th>Absenteeism Rate</th>
                                                    <th>Session 1 Absent</th>
                                                    <th>Session 2 Absent</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="absenteeismTableBody">
                                                <!-- Data will be populated by JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="noDataMessage" class="text-center text-muted" style="display: none;">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h5>No data available for the selected time period</h5>
                                        <p>Try selecting a different time filter or check if attendance has been taken.</p>
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
        };

        // Fetch data for daily attendance
        <?php
        // Update the query for the daily attendance chart to include session information
        $query = "SELECT 
            DATE(dateTimeTaken) as date,
            SUM(CASE WHEN status = 1 AND sessionNumber = 1 THEN 1 ELSE 0 END) as present_s1,
            SUM(CASE WHEN status = 0 AND sessionNumber = 1 THEN 1 ELSE 0 END) as absent_s1,
            SUM(CASE WHEN status = 1 AND sessionNumber = 2 THEN 1 ELSE 0 END) as present_s2,
            SUM(CASE WHEN status = 0 AND sessionNumber = 2 THEN 1 ELSE 0 END) as absent_s2
            FROM tblattendance
            WHERE dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
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
            GROUP BY MONTH(dateTimeTaken)";
        
        $monthlyResult = $conn->query($monthlyQuery);
        $monthlyData = [];
        $monthLabels = [];
        $totalAttendance = 0;
        $totalAbsent = 0;

        if($monthlyResult->num_rows > 0) {
            while($row = $monthlyResult->fetch_assoc()) {
                $monthlyData[] = [$row['month'], $row['present']];
                $monthLabels[] = date('F', mktime(0, 0, 0, $row['month'], 1));
                $totalAttendance += $row['present'];
                $totalAbsent += ($row['total'] - $row['present']);
            }
        }
        
        // Variables for class data (defined but used later)
        $classNames = [];
        $classAttendance = [];
        ?>

        <!-- Add missing PHP variables for charts -->

        function showNoDataMessage(chartId) {
            const ctx = document.getElementById(chartId).getContext('2d');
            ctx.font = '16px Poppins';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#666';
            ctx.fillText('No attendance data available', ctx.canvas.width / 2, ctx.canvas.height / 2);
        }        if (<?php echo $totalAttendance; ?> === 0) {
            showNoDataMessage('monthlyAttendanceChart');
        } else {
            new Chart(document.getElementById('monthlyAttendanceChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [
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
            LEFT JOIN tblattendance a ON c.Id = a.classId
            GROUP BY c.className, c.Id";
        
        $classResult = $conn->query($classQuery);
        $classNames = [];
        $classAttendance = [];

        if($classResult->num_rows > 0) {
            while($row = $classResult->fetch_assoc()) {
                $classNames[] = $row['className'];
                $classAttendance[] = $row['total'] > 0 ? ($row['present'] / $row['total']) * 100 : 0;
            }
        } else {
            $classNames = ['No Data'];
            $classAttendance = [0];
        }
        ?>        if (<?php echo json_encode($classNames); ?>[0] === 'No Data') {
            showNoDataMessage('classAttendanceChart');
        } else {
            new Chart(document.getElementById('classAttendanceChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($classNames); ?>,
                    datasets: [{
                        label: 'Attendance Rate',
                        data: <?php echo json_encode($classAttendance); ?>,
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
        <?php
        // Query to get attendance by subject with session details for TODAY ONLY
        $subjectQuery = "SELECT 
            c.className,
            ca.classArmName as subjectName,
            COUNT(a.id) as total,
            SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 1 THEN 1 ELSE 0 END) as present_s1,
            SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
            SUM(CASE WHEN a.sessionNumber = 1 THEN 1 ELSE 0 END) as total_s1,
            SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 1 THEN 1 ELSE 0 END) as present_s2,
            SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2,
            SUM(CASE WHEN a.sessionNumber = 2 THEN 1 ELSE 0 END) as total_s2
            FROM tblclass c
            JOIN tblclassarms ca ON c.Id = ca.classId
            LEFT JOIN tblattendance a ON ca.Id = a.classArmId 
                AND DATE(a.dateTimeTaken) = CURDATE()
            GROUP BY c.className, ca.classArmName
            HAVING total > 0
            ORDER BY c.className, ca.classArmName";

        $subjectResult = $conn->query($subjectQuery);
        $classSubjects = [];
        $subjectData = [];
        $subjectDetailedData = [];
        $backgroundColors = [];

        if($subjectResult->num_rows > 0) {
            while($row = $subjectResult->fetch_assoc()) {
                $classSubjects[] = $row['className'] . ' - ' . $row['subjectName'];
                $subjectData[] = $row['total'] > 0 ? ($row['present'] / $row['total']) * 100 : 0;
                
                // Store detailed data for tooltips
                $subjectDetailedData[] = [
                    'total' => intval($row['total']),
                    'present' => intval($row['present']),
                    'absent' => intval($row['absent']),
                    'present_s1' => intval($row['present_s1']),
                    'absent_s1' => intval($row['absent_s1']),
                    'total_s1' => intval($row['total_s1']),
                    'present_s2' => intval($row['present_s2']),
                    'absent_s2' => intval($row['absent_s2']),
                    'total_s2' => intval($row['total_s2'])
                ];
                
                // Generate a unique color for each subject
                $hue = mt_rand(0, 360);
                $backgroundColors[] = "hsla({$hue}, 70%, 60%, 0.8)";
            }
        } else {
            $classSubjects = ['No Data Today'];
            $subjectData = [0];
            $subjectDetailedData = [['total' => 0, 'present' => 0, 'absent' => 0, 'present_s1' => 0, 'absent_s1' => 0, 'total_s1' => 0, 'present_s2' => 0, 'absent_s2' => 0, 'total_s2' => 0]];
            $backgroundColors = ['rgba(200, 200, 200, 0.8)'];
        }
        ?>        if (<?php echo json_encode($classSubjects); ?>[0] === 'No Data Today') {
            showNoDataMessage('subjectAttendanceChart');
        } else {
            // Store detailed data globally for tooltip access
            window.subjectDetailedData = <?php echo json_encode($subjectDetailedData); ?>;
            
            new Chart(document.getElementById('subjectAttendanceChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($classSubjects); ?>,                    datasets: [{
                        label: 'Today\'s Attendance Rate by Subject',
                        data: <?php echo json_encode($subjectData); ?>,
                        backgroundColor: <?php echo json_encode($backgroundColors); ?>,
                        borderColor: <?php echo json_encode($backgroundColors); ?>,
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.8,
                        categoryPercentage: 0.9
                    }]
                },
                options: {
                    ...commonOptions,
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
                                    size: 12
                                }
                            },                            title: {
                                display: true,
                                text: 'Today\'s Subject Attendance Rate (%)',
                                font: {
                                    size: 14,
                                    weight: '600'
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
                                    size: 11
                                }
                            },                            title: {
                                display: true,
                                text: 'Today\'s Class - Subject',
                                font: {
                                    size: 14,
                                    weight: '600'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false                        },                        tooltip: {
                            enabled: false, // Disable default tooltip
                            external: function(context) {
                                // Custom tooltip with scroll functionality
                                let tooltipEl = document.getElementById('chartjs-tooltip');
                                
                                // Create element on first render
                                if (!tooltipEl) {
                                    tooltipEl = document.createElement('div');
                                    tooltipEl.id = 'chartjs-tooltip';
                                    tooltipEl.innerHTML = '<div class="tooltip-content"></div>';
                                    document.body.appendChild(tooltipEl);
                                    
                                    // Add mouse events to keep tooltip visible
                                    tooltipEl.addEventListener('mouseenter', function() {
                                        tooltipEl.style.opacity = 1;
                                        clearTimeout(tooltipEl.hideTimeout);
                                    });
                                    
                                    tooltipEl.addEventListener('mouseleave', function() {
                                        tooltipEl.hideTimeout = setTimeout(() => {
                                            tooltipEl.style.opacity = 0;
                                        }, 300);
                                    });
                                }
                                
                                // Hide if no tooltip
                                const tooltipModel = context.tooltip;
                                if (tooltipModel.opacity === 0) {
                                    tooltipEl.hideTimeout = setTimeout(() => {
                                        tooltipEl.style.opacity = 0;
                                    }, 300);
                                    return;
                                }
                                
                                // Clear any pending hide timeout
                                clearTimeout(tooltipEl.hideTimeout);
                                
                                // Set content
                                if (tooltipModel.body) {
                                    const dataIndex = tooltipModel.dataPoints[0].dataIndex;
                                    const data = window.subjectDetailedData[dataIndex];
                                    const label = tooltipModel.dataPoints[0].label;
                                    const value = tooltipModel.dataPoints[0].parsed.y;
                                    
                                    let innerHtml = '<div class="tooltip-header">' + label + '<span class="tooltip-close" onclick="document.getElementById(\'chartjs-tooltip\').style.opacity=0">×</span></div>';
                                    innerHtml += '<div class="tooltip-scroll-content">';
                                    innerHtml += '<div class="tooltip-section">';
                                    innerHtml += '<div class="section-title">📚 Today\'s Subject Overview:</div>';
                                    innerHtml += '<div class="tooltip-item">Attendance Rate: ' + value.toFixed(1) + '%</div>';
                                    innerHtml += '<div class="tooltip-item">Total Students: ' + data.total + ' records</div>';
                                    innerHtml += '<div class="tooltip-item">Total Present: ' + data.present + ' students</div>';
                                    innerHtml += '<div class="tooltip-item">Total Absent: ' + data.absent + ' students</div>';
                                    innerHtml += '</div>';
                                    
                                    innerHtml += '<div class="tooltip-section">';
                                    innerHtml += '<div class="section-title">🕐 Session 1 Details:</div>';
                                    if (data.total_s1 > 0) {
                                        const s1Rate = ((data.present_s1 / data.total_s1) * 100).toFixed(1);
                                        innerHtml += '<div class="tooltip-item">Total Records: ' + data.total_s1 + '</div>';
                                        innerHtml += '<div class="tooltip-item">Present: ' + data.present_s1 + ' students</div>';
                                        innerHtml += '<div class="tooltip-item">Absent: ' + data.absent_s1 + ' students</div>';
                                        innerHtml += '<div class="tooltip-item">Attendance Rate: ' + s1Rate + '%</div>';
                                    } else {
                                        innerHtml += '<div class="tooltip-item">No attendance records</div>';
                                    }
                                    innerHtml += '</div>';
                                    
                                    innerHtml += '<div class="tooltip-section">';
                                    innerHtml += '<div class="section-title">🕑 Session 2 Details:</div>';
                                    if (data.total_s2 > 0) {
                                        const s2Rate = ((data.present_s2 / data.total_s2) * 100).toFixed(1);
                                        innerHtml += '<div class="tooltip-item">Total Records: ' + data.total_s2 + '</div>';
                                        innerHtml += '<div class="tooltip-item">Present: ' + data.present_s2 + ' students</div>';
                                        innerHtml += '<div class="tooltip-item">Absent: ' + data.absent_s2 + ' students</div>';
                                        innerHtml += '<div class="tooltip-item">Attendance Rate: ' + s2Rate + '%</div>';
                                    } else {
                                        innerHtml += '<div class="tooltip-item">No attendance records</div>';
                                    }
                                    innerHtml += '</div>';
                                    innerHtml += '</div>';
                                    
                                    tooltipEl.querySelector('.tooltip-content').innerHTML = innerHtml;
                                }
                                
                                // Position tooltip
                                const position = context.chart.canvas.getBoundingClientRect();
                                
                                // Set styles
                                tooltipEl.style.opacity = 1;
                                tooltipEl.style.position = 'absolute';
                                tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                                tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                                tooltipEl.style.pointerEvents = 'auto'; // Allow interaction
                                tooltipEl.style.zIndex = '9999';
                                
                                // Adjust position if tooltip goes off screen
                                setTimeout(() => {
                                    const tooltipRect = tooltipEl.getBoundingClientRect();
                                    const windowWidth = window.innerWidth;
                                    const windowHeight = window.innerHeight;
                                    
                                    if (tooltipRect.right > windowWidth) {
                                        tooltipEl.style.left = (position.left + window.pageXOffset + tooltipModel.caretX - tooltipRect.width - 10) + 'px';
                                    }
                                    
                                    if (tooltipRect.bottom > windowHeight) {
                                        tooltipEl.style.top = (position.top + window.pageYOffset + tooltipModel.caretY - tooltipRect.height - 10) + 'px';
                                    }
                                }, 0);
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: value => value.toFixed(1) + '%',
                            font: {
                                weight: 'bold'
                            },
                            offset: 4
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    }
                }
            });        }
    });

    // Function to filter absenteeism data
    function filterAbsenteeismData() {
        const filter = document.getElementById('timeFilter').value;
        fetchAbsenteeismData(filter);
    }

    // Function to fetch absenteeism data based on filter
    function fetchAbsenteeismData(filter) {
        // You can make an AJAX call here, but for now I'll use PHP to generate data
        let whereClause = '';
        switch(filter) {
            case 'today':
                whereClause = 'DATE(a.dateTimeTaken) = CURDATE()';
                break;
            case 'week':
                whereClause = 'a.dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)';
                break;
            case 'month':
                whereClause = 'a.dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)';
                break;
            case 'all':
            default:
                whereClause = '1=1';
                break;
        }
        
        // Since we can't make AJAX calls easily here, let's generate data for all filters
        // and filter them in JavaScript
        generateAbsenteeismTable(filter);
    }

    // Function to generate status badge
    function getStatusBadge(attendanceRate) {
        if (attendanceRate < 60) {
            return '<span class="status-badge status-critical">Critical</span>';
        } else if (attendanceRate < 80) {
            return '<span class="status-badge status-warning">Warning</span>';
        } else {
            return '<span class="status-badge status-good">Good</span>';
        }
    }

    // Function to get rate class
    function getRateClass(rate) {
        if (rate < 60) return 'rate-low';
        if (rate < 80) return 'rate-medium';
        return 'rate-high';
    }

    // Generate absenteeism table
    function generateAbsenteeismTable(filter) {
        <?php        // Generate data for all time periods
        $absenteeismQueries = [            'today' => "SELECT 
                c.className,
                ca.classArmName as subjectName,
                COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
                SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2
                FROM tblattendance a
                LEFT JOIN tblclass c ON a.classId = c.Id
                LEFT JOIN tblclassarms ca ON a.classArmId = ca.Id
                WHERE DATE(a.dateTimeTaken) = CURDATE()
                GROUP BY a.classId, a.classArmId, c.className, ca.classArmName
                ORDER BY absent DESC",
              'week' => "SELECT 
                c.className,
                ca.classArmName as subjectName,
                COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
                SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2
                FROM tblattendance a
                LEFT JOIN tblclass c ON a.classId = c.Id
                LEFT JOIN tblclassarms ca ON a.classArmId = ca.Id
                WHERE a.dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY a.classId, a.classArmId, c.className, ca.classArmName
                ORDER BY absent DESC",
              'month' => "SELECT 
                c.className,
                ca.classArmName as subjectName,
                COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
                SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2
                FROM tblattendance a
                LEFT JOIN tblclass c ON a.classId = c.Id
                LEFT JOIN tblclassarms ca ON a.classArmId = ca.Id
                WHERE a.dateTimeTaken >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY a.classId, a.classArmId, c.className, ca.classArmName
                ORDER BY absent DESC",
              'all' => "SELECT 
                c.className,
                ca.classArmName as subjectName,
                COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
                SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2
                FROM tblattendance a
                LEFT JOIN tblclass c ON a.classId = c.Id
                LEFT JOIN tblclassarms ca ON a.classArmId = ca.Id
                GROUP BY a.classId, a.classArmId, c.className, ca.classArmName
                ORDER BY absent DESC"
        ];

        $absenteeismData = [];
        foreach($absenteeismQueries as $period => $query) {
            $result = $conn->query($query);
            $absenteeismData[$period] = [];            if($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Handle cases where class or subject names might be null
                    $className = $row['className'] ? $row['className'] : 'Unknown Class';
                    $subjectName = $row['subjectName'] ? $row['subjectName'] : 'Unknown Subject';
                    
                    $absenteeismData[$period][] = [
                        'className' => $className,
                        'subjectName' => $subjectName,
                        'total' => intval($row['total']),
                        'present' => intval($row['present']),
                        'absent' => intval($row['absent']),
                        'absent_s1' => intval($row['absent_s1']),
                        'absent_s2' => intval($row['absent_s2'])
                    ];
                }
            }}
        
        // Debug: Let's check what dates we have in the attendance table
        $dateCheckQuery = "SELECT DATE(dateTimeTaken) as attendance_date, COUNT(*) as records 
                          FROM tblattendance 
                          GROUP BY DATE(dateTimeTaken) 
                          ORDER BY attendance_date DESC 
                          LIMIT 10";
        $dateCheckResult = $conn->query($dateCheckQuery);
        $availableDates = [];
        if($dateCheckResult && $dateCheckResult->num_rows > 0) {
            while($row = $dateCheckResult->fetch_assoc()) {
                $availableDates[] = $row['attendance_date'] . ' (' . $row['records'] . ' records)';
            }
        }
        
        // Debug: Let's also create a simple query to check if we have any data at all
        $debugQuery = "SELECT COUNT(*) as total_records FROM tblattendance";
        $debugResult = $conn->query($debugQuery);
        $totalAttendanceRecords = $debugResult ? $debugResult->fetch_assoc()['total_records'] : 0;
        
        // Check class and subject data
        $classQuery = "SELECT COUNT(*) as total_classes FROM tblclass";
        $classResult = $conn->query($classQuery);
        $totalClasses = $classResult ? $classResult->fetch_assoc()['total_classes'] : 0;
          $subjectQuery = "SELECT COUNT(*) as total_subjects FROM tblclassarms";
        $subjectResult = $conn->query($subjectQuery);
        $totalSubjects = $subjectResult ? $subjectResult->fetch_assoc()['total_subjects'] : 0;
        
        // Fallback: If the complex queries don't work, try a simpler approach
        if (empty($absenteeismData['all'])) {
            $simpleQuery = "SELECT 
                a.classId,
                a.classArmId,
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s1,
                SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 0 THEN 1 ELSE 0 END) as absent_s2
                FROM tblattendance a
                GROUP BY a.classId, a.classArmId
                ORDER BY absent DESC";
            
            $simpleResult = $conn->query($simpleQuery);
            if ($simpleResult && $simpleResult->num_rows > 0) {
                while($row = $simpleResult->fetch_assoc()) {
                    // Get class and subject names separately
                    $classNameQuery = "SELECT className FROM tblclass WHERE Id = " . $row['classId'];
                    $classNameResult = $conn->query($classNameQuery);
                    $className = $classNameResult ? $classNameResult->fetch_assoc()['className'] : 'Unknown Class';
                    
                    $subjectNameQuery = "SELECT classArmName FROM tblclassarms WHERE Id = " . $row['classArmId'];
                    $subjectNameResult = $conn->query($subjectNameQuery);
                    $subjectName = $subjectNameResult ? $subjectNameResult->fetch_assoc()['classArmName'] : 'Unknown Subject';
                    
                    $absenteeismData['all'][] = [
                        'className' => $className,
                        'subjectName' => $subjectName,
                        'total' => intval($row['total']),
                        'present' => intval($row['present']),
                        'absent' => intval($row['absent']),
                        'absent_s1' => intval($row['absent_s1']),
                        'absent_s2' => intval($row['absent_s2'])
                    ];
                }
            }
        }
        ?>        const absenteeismData = <?php echo json_encode($absenteeismData); ?>;
          // Debug information
        console.log('Debug Info:');
        console.log('Total Attendance Records: <?php echo $totalAttendanceRecords; ?>');
        console.log('Total Classes: <?php echo $totalClasses; ?>');
        console.log('Total Subjects: <?php echo $totalSubjects; ?>');
        console.log('Available Dates:', <?php echo json_encode($availableDates); ?>);
        console.log('Current Date: <?php echo date('Y-m-d'); ?>');
        console.log('Absenteeism Data:', absenteeismData);
        
        const data = absenteeismData[filter] || [];
        
        const tableBody = document.getElementById('absenteeismTableBody');
        const noDataMessage = document.getElementById('noDataMessage');
        
        if (data.length === 0) {
            tableBody.innerHTML = '';
            noDataMessage.style.display = 'block';            // Update the no data message to be more specific
            const debugInfo = `
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>No data available for the selected time period</h5>
                <p>Debug Information:</p>
                <ul class="text-left" style="display: inline-block;">
                    <li>Total Attendance Records: <?php echo $totalAttendanceRecords; ?></li>
                    <li>Total Classes: <?php echo $totalClasses; ?></li>
                    <li>Total Subjects: <?php echo $totalSubjects; ?></li>
                    <li>Selected Filter: ${filter}</li>
                    <li>Current Date: <?php echo date('Y-m-d'); ?></li>
                    <li>Available Dates: <?php echo implode(', ', $availableDates); ?></li>
                </ul>
                <p><strong>Tip:</strong> If you see dates above but no data for "Today", it means no attendance was taken today. Try "All Time" to see historical data.</p>
            `;
            noDataMessage.innerHTML = debugInfo;
            return;
        }
        
        noDataMessage.style.display = 'none';
        
        let html = '';
        data.forEach((item, index) => {
            const attendanceRate = item.total > 0 ? (item.present / item.total * 100) : 0;
            const absenteeismRate = 100 - attendanceRate;
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.className}</strong></td>
                    <td>${item.subjectName}</td>
                    <td>${item.total}</td>
                    <td><span class="present-count">${item.present}</span></td>
                    <td><span class="absent-count">${item.absent}</span></td>
                    <td><span class="attendance-rate ${getRateClass(attendanceRate)}">${attendanceRate.toFixed(1)}%</span></td>
                    <td><span class="attendance-rate rate-low">${absenteeismRate.toFixed(1)}%</span></td>
                    <td><span class="absent-count">${item.absent_s1}</span></td>
                    <td><span class="absent-count">${item.absent_s2}</span></td>
                    <td>${getStatusBadge(attendanceRate)}</td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
    }    // Initialize the table with default filter
    generateAbsenteeismTable('all');    // Analytics Report Generation Functions
    function generateAnalyticsReport() {
        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Show loading indicator on button
        const loadingBtn = event.target;
        const originalText = loadingBtn.innerHTML;
        loadingBtn.innerHTML = '<i class="fas fa-spinner fa-spin fa-sm text-white-50"></i> Generating Report...';
        loadingBtn.disabled = true;

        // Collect all analytics data
        const reportData = {
            generationDate: new Date().toLocaleString(),
            summary: {
                totalClasses: <?php echo $totalClasses; ?>,
                totalSubjects: <?php echo $totalSubjects; ?>,
                totalAttendanceRecords: <?php echo $totalAttendanceRecords; ?>,
                availableDates: <?php echo json_encode($availableDates); ?>
            },            monthlyData: <?php echo json_encode($monthlyData); ?>,
            classData: <?php echo json_encode(array_map(function($class, $percentage) { 
                return ['class' => $class, 'attendance' => $percentage]; 
            }, $classNames, $classAttendance)); ?>,
            subjectData: <?php echo json_encode(array_map(function($subject, $percentage, $details) { 
                return [
                    'subject' => $subject, 
                    'attendance' => $percentage,
                    'total' => $details['total'],
                    'present' => $details['present'],
                    'absent' => $details['absent'],
                    'session1_present' => $details['present_s1'],
                    'session1_absent' => $details['absent_s1'],
                    'session2_present' => $details['present_s2'],
                    'session2_absent' => $details['absent_s2']
                ]; 
            }, $classSubjects, $subjectData, $subjectDetailedData)); ?>,
            absenteeismData: <?php echo json_encode($absenteeismData); ?>
        };

        // Create and submit form to generate Excel report
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'generateAnalyticsReport.php';
        form.style.display = 'none';

        const dataInput = document.createElement('input');
        dataInput.type = 'hidden';
        dataInput.name = 'reportData';
        dataInput.value = JSON.stringify(reportData);
        form.appendChild(dataInput);        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        // Reset button and hide loading after delay
        setTimeout(() => {
            loadingBtn.innerHTML = originalText;
            loadingBtn.disabled = false;
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 3000);
    }

    function printAnalytics() {
        // Create print-friendly version
        const printWindow = window.open('', '_blank');
        const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Analytics Report - <?php echo date('Y-m-d'); ?></title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .section { margin-bottom: 30px; page-break-inside: avoid; }
                    .section h3 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .summary-stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
                    .stat-box { text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                    @media print { .section { page-break-inside: avoid; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Attendance Analytics Report</h1>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                    <p>Institution: QIU Student Attendance System</p>
                </div>

                <div class="section">
                    <h3>Summary Statistics</h3>
                    <div class="summary-stats">
                        <div class="stat-box">
                            <h4><?php echo $totalClasses; ?></h4>
                            <p>Total Classes</p>
                        </div>
                        <div class="stat-box">
                            <h4><?php echo $totalSubjects; ?></h4>
                            <p>Total Subjects</p>
                        </div>
                        <div class="stat-box">
                            <h4><?php echo $totalAttendanceRecords; ?></h4>
                            <p>Attendance Records</p>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3>Class-wise Attendance Summary</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Attendance Rate</th>
                                <th>Status</th>
                            </tr>
                        </thead>                        <tbody>
                            ${<?php echo json_encode($classNames); ?>.map((className, index) => 
                                `<tr>
                                    <td>${className}</td>
                                    <td>${<?php echo json_encode($classAttendance); ?>[index].toFixed(1)}%</td>
                                    <td>${<?php echo json_encode($classAttendance); ?>[index] >= 80 ? 'Good' : 
                                         <?php echo json_encode($classAttendance); ?>[index] >= 60 ? 'Fair' : 'Poor'}</td>
                                </tr>`
                            ).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Subject-wise Attendance Analysis</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Total Records</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Attendance Rate</th>
                                <th>Session 1 Status</th>
                                <th>Session 2 Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${<?php echo json_encode($classSubjects); ?>.map((subject, index) => {
                                const details = <?php echo json_encode($subjectDetailedData); ?>[index];
                                const rate = <?php echo json_encode($subjectData); ?>[index];
                                return `<tr>
                                    <td>${subject}</td>
                                    <td>${details.total}</td>
                                    <td>${details.present}</td>
                                    <td>${details.absent}</td>
                                    <td>${rate.toFixed(1)}%</td>
                                    <td>${details.total_s1 > 0 ? (details.present_s1/details.total_s1*100).toFixed(1)+'%' : 'N/A'}</td>
                                    <td>${details.total_s2 > 0 ? (details.present_s2/details.total_s2*100).toFixed(1)+'%' : 'N/A'}</td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>                <div class="section">
                    <h3>Monthly Attendance Trend</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Attendance Count</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${<?php echo json_encode($monthLabels); ?>.map((month, index) => {
                                const current = <?php echo json_encode(array_column($monthlyData, 1)); ?>[index];
                                return `<tr>
                                    <td>${month}</td>
                                    <td>${current}</td>
                                    <td>${current > 100 ? 'High' : current > 50 ? 'Medium' : 'Low'}</td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Recommendations</h3>
                    <ul>
                        <li><strong>High Priority:</strong> Focus on classes/subjects with attendance below 60%</li>
                        <li><strong>Medium Priority:</strong> Monitor classes/subjects with attendance between 60-79%</li>
                        <li><strong>Session Analysis:</strong> Compare Session 1 vs Session 2 attendance patterns</li>
                        <li><strong>Trend Monitoring:</strong> Track monthly improvements or declines</li>
                        <li><strong>Intervention Needed:</strong> Consider additional support for consistently low-performing areas</li>
                    </ul>
                </div>
            </body>
            </html>
        `;
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 500);    }
    </script>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Generating Analytics Report...</h5>
            <p>Please wait while we compile your comprehensive analytics data into an Excel report.</p>
        </div>
    </div>
</body>
</html>