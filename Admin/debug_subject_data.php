<?php
header('Content-Type: application/json');
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get the filter from POST data
$input = json_decode(file_get_contents('php://input'), true);
$filter = isset($input['filter']) ? $input['filter'] : 'all';

// Build the date condition based on filter
$dateCondition = '';
switch ($filter) {
    case 'today':
        $dateCondition = "AND STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d') = CURDATE()";
        break;
    case 'week':
        $dateCondition = "AND STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $dateCondition = "AND STR_TO_DATE(a.dateTimeTaken, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'all':
    default:
        $dateCondition = '';
        break;
}

$debug = [];
$debug['filter_received'] = $filter;
$debug['date_condition'] = $dateCondition;
$debug['current_date'] = date('Y-m-d H:i:s');

try {    // Check what dates we have in the database
    $dateCheckQuery = "SELECT 
        dateTimeTaken as date,
        COUNT(*) as count
        FROM tblattendance 
        GROUP BY dateTimeTaken
        ORDER BY dateTimeTaken DESC 
        LIMIT 10";
    
    $dateResult = $conn->query($dateCheckQuery);
    $availableDates = [];
    if ($dateResult && $dateResult->num_rows > 0) {
        while ($row = $dateResult->fetch_assoc()) {
            $availableDates[] = $row;
        }
    }
    $debug['available_dates'] = $availableDates;

    // Test the filtered query
    $testQuery = "SELECT COUNT(*) as count FROM tblattendance";
    if (!empty($dateCondition)) {
        $testQuery .= " WHERE " . ltrim($dateCondition, 'AND ');
    }
    $debug['test_query'] = $testQuery;
    
    $testResult = $conn->query($testQuery);
    if ($testResult) {
        $testRow = $testResult->fetch_assoc();
        $debug['filtered_count'] = $testRow['count'];
    }

    // Query to get attendance by subject with date filtering
    $subjectQuery = "SELECT 
        c.className,
        ca.classArmName as subjectName,
        COUNT(a.id) as total,
        SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present,
        DATE(a.dateTimeTaken) as attendance_date
        FROM tblclass c
        JOIN tblclassarms ca ON c.Id = ca.classId
        LEFT JOIN tblattendance a ON ca.Id = a.classArmId";
        
    // Add the WHERE clause properly
    if (!empty($dateCondition)) {
        $subjectQuery .= " WHERE " . ltrim($dateCondition, 'AND ');
    }
    
    $subjectQuery .= " GROUP BY c.Id, c.className, ca.Id, ca.classArmName
        HAVING total > 0
        ORDER BY c.className, ca.classArmName";
    
    $debug['subject_query'] = $subjectQuery;

    $subjectResult = $conn->query($subjectQuery);
    $classSubjects = [];
    $subjectData = [];
    $backgroundColors = [];
    $presentCounts = [];
    $totalCounts = [];

    if ($subjectResult && $subjectResult->num_rows > 0) {
        // Predefined color palette for consistency
        $colorPalette = [
            'rgba(52, 152, 219, 0.8)',   // Blue
            'rgba(46, 204, 113, 0.8)',   // Green
            'rgba(155, 89, 182, 0.8)',   // Purple
            'rgba(241, 196, 15, 0.8)',   // Yellow
            'rgba(230, 126, 34, 0.8)',   // Orange
            'rgba(231, 76, 60, 0.8)',    // Red
        ];
        
        $colorIndex = 0;
        
        while ($row = $subjectResult->fetch_assoc()) {
            $classSubjects[] = $row['className'] . ' - ' . $row['subjectName'];
            $attendanceRate = $row['total'] > 0 ? ($row['present'] / $row['total']) * 100 : 0;
            $subjectData[] = round($attendanceRate, 1);
            
            // Use color palette with cycling
            $backgroundColors[] = $colorPalette[$colorIndex % count($colorPalette)];
            $colorIndex++;
            
            // Store raw numbers for tooltip
            $presentCounts[] = intval($row['present']);
            $totalCounts[] = intval($row['total']);
        }
        
        $debug['result_count'] = $subjectResult->num_rows;
    } else {
        $debug['result_count'] = 0;
        $debug['query_error'] = $conn->error;
    }

    // Return the data as JSON with debug info
    echo json_encode([
        'success' => true,
        'classSubjects' => $classSubjects,
        'subjectData' => $subjectData,
        'backgroundColors' => $backgroundColors,
        'presentCounts' => $presentCounts,
        'totalCounts' => $totalCounts,
        'filter' => $filter,
        'recordCount' => count($classSubjects),
        'debug' => $debug
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'debug' => $debug
    ]);
}

$conn->close();
?>
