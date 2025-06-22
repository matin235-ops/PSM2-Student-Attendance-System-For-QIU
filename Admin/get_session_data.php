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

try {
    // First, let's check if we have any session data at all
    $checkSessionQuery = "SELECT COUNT(*) as count FROM tblattendance WHERE sessionNumber IS NOT NULL AND sessionNumber != ''";
    $checkResult = $conn->query($checkSessionQuery);
    $hasSessionData = false;
    
    if ($checkResult) {
        $checkRow = $checkResult->fetch_assoc();
        $hasSessionData = $checkRow['count'] > 0;
    }
    
    if (!$hasSessionData) {
        echo json_encode([
            'success' => true,
            'classNames' => [],
            'session1Rate' => [],
            'session2Rate' => [],
            'session1Present' => [],
            'session1Total' => [],
            'session2Present' => [],
            'session2Total' => [],
            'filter' => $filter,
            'message' => 'No session data available in the system'
        ]);
        exit;
    }

    // Query for session comparison with date filtering
    $sessionCompQuery = "SELECT 
        c.className,
        SUM(CASE WHEN a.sessionNumber = 1 AND a.status = 1 THEN 1 ELSE 0 END) as present_s1,
        COUNT(CASE WHEN a.sessionNumber = 1 THEN 1 ELSE NULL END) as total_s1,
        SUM(CASE WHEN a.sessionNumber = 2 AND a.status = 1 THEN 1 ELSE 0 END) as present_s2,
        COUNT(CASE WHEN a.sessionNumber = 2 THEN 1 ELSE NULL END) as total_s2
        FROM tblclass c
        LEFT JOIN tblattendance a ON c.Id = a.classId AND a.sessionNumber IS NOT NULL AND a.sessionNumber != ''
        WHERE 1=1 $dateCondition
        GROUP BY c.Id, c.className
        HAVING (total_s1 > 0 OR total_s2 > 0)
        ORDER BY c.className";
        
    $sessionResult = $conn->query($sessionCompQuery);
    
    $classNames = [];
    $session1Rate = [];
    $session2Rate = [];
    $session1Present = [];
    $session1Total = [];
    $session2Present = [];
    $session2Total = [];

    if ($sessionResult && $sessionResult->num_rows > 0) {
        while ($row = $sessionResult->fetch_assoc()) {
            $classNames[] = $row['className'];
            
            // Calculate attendance rates with better error handling
            $s1Rate = $row['total_s1'] > 0 ? ($row['present_s1'] / $row['total_s1']) * 100 : 0;
            $s2Rate = $row['total_s2'] > 0 ? ($row['present_s2'] / $row['total_s2']) * 100 : 0;
            
            $session1Rate[] = round($s1Rate, 1);
            $session2Rate[] = round($s2Rate, 1);
            
            // Store raw numbers for tooltip
            $session1Present[] = intval($row['present_s1']);
            $session1Total[] = intval($row['total_s1']);
            $session2Present[] = intval($row['present_s2']);
            $session2Total[] = intval($row['total_s2']);
        }
    }

    // Return the data as JSON
    echo json_encode([
        'success' => true,
        'classNames' => $classNames,
        'session1Rate' => $session1Rate,
        'session2Rate' => $session2Rate,
        'session1Present' => $session1Present,
        'session1Total' => $session1Total,
        'session2Present' => $session2Present,
        'session2Total' => $session2Total,
        'filter' => $filter,
        'recordCount' => count($classNames)
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
