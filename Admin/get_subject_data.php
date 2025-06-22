<?php
header('Content-Type: application/json');
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get the filter from POST data
$input = json_decode(file_get_contents('php://input'), true);
$filter = isset($input['filter']) ? $input['filter'] : 'all';

// Build the date condition based on filter
$dateCondition = '';
$dateConditionForCheck = '';
switch ($filter) {
    case 'today':
        $dateCondition = "AND DATE(a.dateTimeTaken) = CURDATE()";
        $dateConditionForCheck = "AND DATE(dateTimeTaken) = CURDATE()";
        break;
    case 'week':
        $dateCondition = "AND DATE(a.dateTimeTaken) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $dateConditionForCheck = "AND DATE(dateTimeTaken) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $dateCondition = "AND DATE(a.dateTimeTaken) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $dateConditionForCheck = "AND DATE(dateTimeTaken) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'all':
    default:
        $dateCondition = '';
        $dateConditionForCheck = '';
        break;
}

$debugInfo = [
    'filter' => $filter,
    'dateCondition' => $dateCondition,
    'checkDataQuery' => '',
    'checkDataCount' => null,
    'subjectQuery' => '',
    'subjectRows' => null,
    'dbErrors' => []
];

try {    // First, let's check if we have any attendance data at all for the selected period
    $checkDataQuery = "SELECT COUNT(*) as count FROM tblattendance";    if (!empty($dateConditionForCheck)) {
        $checkDataQuery .= " WHERE " . substr($dateConditionForCheck, 4); // Remove "AND " prefix
    }
    $debugInfo['checkDataQuery'] = $checkDataQuery;
    $checkResult = $conn->query($checkDataQuery);
    $hasData = false;
    if ($checkResult) {
        $checkRow = $checkResult->fetch_assoc();
        $hasData = $checkRow['count'] > 0;
        $debugInfo['checkDataCount'] = $checkRow['count'];
    } else {
        $debugInfo['dbErrors'][] = $conn->error;
    }
    if (!$hasData) {
        echo json_encode([
            'success' => true,
            'classSubjects' => [],
            'subjectData' => [],
            'backgroundColors' => [],
            'presentCounts' => [],
            'totalCounts' => [],
            'filter' => $filter,
            'message' => 'No attendance data available for the selected period',
            'debug' => $debugInfo
        ]);
        exit;
    }    // Query to get attendance by subject with date filtering
    $subjectQuery = "SELECT 
        c.className,
        ca.classArmName as subjectName,
        COUNT(a.id) as total,
        SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) as present
        FROM tblclass c
        JOIN tblclassarms ca ON c.Id = ca.classId
        LEFT JOIN tblattendance a ON ca.Id = a.classArmId";
          // Add the WHERE clause properly
    if (!empty($dateCondition)) {
        $subjectQuery .= " WHERE " . substr($dateCondition, 4); // Remove "AND " prefix
    }
    
    $subjectQuery .= " GROUP BY c.Id, c.className, ca.Id, ca.classArmName
        HAVING total > 0
        ORDER BY c.className, ca.classArmName";
    $debugInfo['subjectQuery'] = $subjectQuery;
    $subjectResult = $conn->query($subjectQuery);
    $classSubjects = [];
    $subjectData = [];
    $backgroundColors = [];
    $presentCounts = [];
    $totalCounts = [];

    if ($subjectResult && $subjectResult->num_rows > 0) {
        $debugInfo['subjectRows'] = $subjectResult->num_rows;
        
        // Predefined color palette for consistency
        $colorPalette = [
            'rgba(52, 152, 219, 0.8)',   // Blue
            'rgba(46, 204, 113, 0.8)',   // Green
            'rgba(155, 89, 182, 0.8)',   // Purple
            'rgba(241, 196, 15, 0.8)',   // Yellow
            'rgba(230, 126, 34, 0.8)',   // Orange
            'rgba(231, 76, 60, 0.8)',    // Red
            'rgba(52, 73, 94, 0.8)',     // Dark Blue
            'rgba(26, 188, 156, 0.8)',   // Teal
            'rgba(243, 156, 18, 0.8)',   // Orange-Yellow
            'rgba(142, 68, 173, 0.8)',   // Dark Purple
            'rgba(39, 174, 96, 0.8)',    // Dark Green
            'rgba(192, 57, 43, 0.8)',    // Dark Red
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
    } else {
        $debugInfo['subjectRows'] = 0;
        if ($conn->error) {
            $debugInfo['dbErrors'][] = $conn->error;
        }
    }

    // Return the data as JSON
    echo json_encode([
        'success' => true,
        'classSubjects' => $classSubjects,
        'subjectData' => $subjectData,
        'backgroundColors' => $backgroundColors,
        'presentCounts' => $presentCounts,
        'totalCounts' => $totalCounts,
        'filter' => $filter,
        'recordCount' => count($classSubjects),
        'debug' => $debugInfo
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
