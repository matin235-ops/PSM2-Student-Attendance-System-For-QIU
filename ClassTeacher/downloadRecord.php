<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Debug logging
$logFile = 'download_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Download attempted\n", FILE_APPEND);
file_put_contents($logFile, "Parameters: " . print_r($_GET, true) . "\n", FILE_APPEND);

// Error handler
function handleError($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Error: $message\n", FILE_APPEND);
    die("<script>
        alert('Error: $message');
        window.history.back();
    </script>");
}

// Get parameters
$classInfo = isset($_GET['classInfo']) ? $_GET['classInfo'] : '';
list($classId, $armId) = !empty($classInfo) ? explode(':', $classInfo) : [null, null];
$fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// Validate parameters with better error messages
if (empty($classInfo)) {
    handleError("Please select a class");
}

if (!$classId || !$armId) {
    handleError("Invalid class information format");
}

// Add parameter logging for debugging
file_put_contents($logFile, "ClassInfo: $classInfo\n", FILE_APPEND);
file_put_contents($logFile, "ClassId: $classId, ArmId: $armId\n", FILE_APPEND);

// Verify teacher access
$teacherId = $_SESSION['userId'];
$accessQuery = "SELECT COUNT(*) as count FROM teacher_classes 
               WHERE teacher_id = ? AND class_id = ? AND class_arm_id = ?";
$stmt = $conn->prepare($accessQuery);
$stmt->bind_param("iii", $teacherId, $classId, $armId);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()['count'] == 0) {
    handleError("Access denied to this class");
}

// Get class and subject info first
$classInfoQuery = "SELECT c.className, ca.classArmName 
                  FROM tblclass c 
                  JOIN tblclassarms ca ON ca.classId = c.Id 
                  WHERE c.Id = ? AND ca.Id = ?";
$stmtInfo = $conn->prepare($classInfoQuery);
$stmtInfo->bind_param("ii", $classId, $armId);
$stmtInfo->execute();
$classInfo = $stmtInfo->get_result()->fetch_assoc();

// Get attendance data
$query = "SELECT 
    s.admissionNumber,
    s.firstName,
    s.lastName,
    c.className,
    ca.classArmName as subjectName,
    DATE(a.dateTimeTaken) as attendanceDate,
    MAX(CASE WHEN a.sessionNumber = 1 THEN a.status END) as session1_status,
    MAX(CASE WHEN a.sessionNumber = 2 THEN a.status END) as session2_status
FROM tblstudents s
INNER JOIN tblclass c ON c.Id = s.classId
INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
LEFT JOIN tblattendance a ON s.admissionNumber = a.admissionNo 
    AND DATE(a.dateTimeTaken) BETWEEN ? AND ?
WHERE s.classId = ? AND s.classArmId = ?
GROUP BY s.admissionNumber, DATE(a.dateTimeTaken)
ORDER BY s.admissionNumber, attendanceDate";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $fromDate, $toDate, $classId, $armId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    handleError("Failed to fetch attendance data: " . $conn->error);
}

// Set filename and headers
$filename = "Attendance_Report_{$fromDate}_to_{$toDate}.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

// Update the style section with professional design
echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' 
            xmlns:x='urn:schemas-microsoft-com:office:excel'>
<head>
    <meta charset='UTF-8'>
    <style>
        /* Professional Excel styles */
        .report-header {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            background: #1e88e5;
            color: white;
            padding: 10px;
            border-bottom: 2px solid #0d47a1;
        }
        .date-range {
            font-size: 11pt;
            text-align: right;
            padding: 5px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .table-header {
            background: #2196F3;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        th {
            background: #2196F3;
            color: white;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #1976D2;
            text-align: center;
            vertical-align: middle;
        }
        td {
            padding: 6px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
            mso-number-format: '\\@';
        }
        .status-present {
            background: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 12px;
            mso-pattern: #4CAF50 solid;
        }
        .status-absent {
            background: #f44336;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 12px;
            mso-pattern: #f44336 solid;
        }
        .row-even {
            background: #f8f9fa;
        }
        .row-odd {
            background: white;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            margin: 2px 8px;  /* Increased margin */
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
            mso-style-parent: style0;
        }
    </style>
</head>
<body>";

// Update the report header section
echo "<table width='100%' cellpadding='0' cellspacing='0'>
        <tr>
            <td colspan='7' class='report-header'>
                <div style='font-size: 20pt; margin-bottom: 5px;'>QIU ATTENDANCE REPORT</div>
                <div style='font-size: 12pt;'>" . 
                    htmlspecialchars($classInfo['className']) . " - " . 
                    htmlspecialchars($classInfo['classArmName']) . 
                "</div>
            </td>
        </tr>
        <tr>
            <td colspan='7' class='date-range'>
                Period: " . date('d/m/Y', strtotime($fromDate)) . " - " . date('d/m/Y', strtotime($toDate)) . "
            </td>
        </tr>
    </table>";

// Update the main table
echo "<table border='1' cellspacing='0' cellpadding='5' width='100%' style='margin-top: 10px;'>";

// Update the table headers
echo "<thead>
        <tr class='table-header'>
            <th>#</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Stage & Group</th>
            <th>Subject</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
      </thead><tbody>";

// Update the display section
$sn = 0;
while ($row = $result->fetch_assoc()) {
    $sn++;
    $rowClass = $sn % 2 == 0 ? 'row-even' : 'row-odd';
    
    echo "<tr class='{$rowClass}'>
        <td style='width: 5%;'>$sn</td>
        <td style='width: 15%;'>" . htmlspecialchars($row['admissionNumber']) . "</td>
        <td style='width: 20%; text-align: left;'>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</td>
        <td style='width: 15%;'>" . htmlspecialchars($row['className']) . "</td>
        <td style='width: 15%;'>" . htmlspecialchars($row['subjectName']) . "</td>
        <td style='width: 15%;'>" . date('d/m/Y', strtotime($row['attendanceDate'])) . "</td>
        <td style='width: 15%; padding: 4px;'>";
    
    if (!is_null($row['session1_status']) || !is_null($row['session2_status'])) {
        if (!is_null($row['session1_status'])) {
            $s1Status = $row['session1_status'] == '1' ? 'Present' : 'Absent';
            $s1Class = $row['session1_status'] == '1' ? 'status-present' : 'status-absent';
            echo "<span class='status-badge {$s1Class}'>S1: {$s1Status}</span>";
        }
        
        // Add explicit spacing between sessions
        echo "&nbsp;&nbsp;&nbsp;&nbsp;"; // Added more non-breaking spaces
        
        if (!is_null($row['session2_status'])) {
            $s2Status = $row['session2_status'] == '1' ? 'Present' : 'Absent';
            $s2Class = $row['session2_status'] == '1' ? 'status-present' : 'status-absent';
            echo "<span class='status-badge {$s2Class}'>S2: {$s2Status}</span>";
        }
    } else {
        echo "<span style='color: #757575;'>Not taken</span>";
    }
    
    echo "</td></tr>";
}

echo "</tbody></table>";
echo "<div style='text-align: right; margin-top: 10px; font-size: 10pt; color: #666;'>
        Generated on: ".date('d/m/Y H:i')."
      </div>";
echo "</body></html>";
?>
