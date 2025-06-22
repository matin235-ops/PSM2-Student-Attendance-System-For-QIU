<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Validate input parameters
if (!isset($_POST['reportType'])) {
    header("Location: index.php");
    exit();
}

$reportType = $_POST['reportType'];
$fromDate = ($reportType === 'today') ? date('Y-m-d') : $_POST['fromDate'];
$toDate = ($reportType === 'today') ? date('Y-m-d') : $_POST['toDate'];

// Validate dates
if (!$fromDate || !$toDate) {
    header("Location: index.php?error=invalid_dates");
    exit();
}

// Ensure fromDate is not greater than toDate
if ($fromDate > $toDate) {
    $temp = $fromDate;
    $fromDate = $toDate;
    $toDate = $temp;
}

// Set headers for Excel download
$filename = "Attendance_Report_{$fromDate}_to_{$toDate}.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' 
            xmlns:x='urn:schemas-microsoft-com:office:excel'>
<head>
    <meta charset='UTF-8'>
    <style>
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
            mso-pattern: #4CAF50 solid;
        }
        .status-absent {
            background: #f44336;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            mso-pattern: #f44336 solid;
        }
        .row-even { background: #f8f9fa; }
        .row-odd { background: white; }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            margin: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }
        .class-header {
            background: #1a237e !important;
            color: white !important;
            font-weight: bold !important;
            padding: 12px 8px !important;
            text-align: left !important;
            border: 1px solid #0d47a1 !important;
            mso-pattern: #1a237e solid;
        }
        
        .class-group {
            margin-top: 10px;
        }
    </style>
</head>
<body>";

// Report Header
echo "<table width='100%' cellpadding='0' cellspacing='0'>
        <tr>
            <td colspan='7' class='report-header'>
                <div style='font-size: 20pt; margin-bottom: 5px;'>QIU ATTENDANCE REPORT</div>
                <div style='font-size: 12pt;'>Administrative Overview</div>
            </td>
        </tr>        <tr>
            <td colspan='7' class='date-range'>
                Period: " . date('d/m/Y', strtotime($fromDate)) . 
                ($fromDate === $toDate ? " (Today)" : " - " . date('d/m/Y', strtotime($toDate))) . "
                <br>Report Type: " . ucfirst($reportType) . "
            </td>
        </tr>
    </table>";

// Main attendance table
echo "<table border='1' cellspacing='0' cellpadding='5' width='100%' style='margin-top: 10px;'>
        <thead>
            <tr class='table-header'>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Stage & Group</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Session Status</th>
            </tr>
        </thead><tbody>";

// Modified query to only show students with attendance records in the date range
$query = "SELECT 
    c.className,
    ca.classArmName,
    s.admissionNumber,
    s.firstName,
    s.lastName,
    DATE(a.dateTimeTaken) as attendanceDate,
    MAX(CASE WHEN a.sessionNumber = 1 THEN a.status END) as session1_status,
    MAX(CASE WHEN a.sessionNumber = 2 THEN a.status END) as session2_status
FROM tblclass c
INNER JOIN tblstudents s ON c.Id = s.classId
INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
INNER JOIN tblattendance a ON s.admissionNumber = a.admissionNo 
    AND DATE(a.dateTimeTaken) BETWEEN ? AND ?
GROUP BY c.className, s.admissionNumber, DATE(a.dateTimeTaken)
HAVING attendanceDate IS NOT NULL
ORDER BY c.className, ca.classArmName, s.firstName, s.lastName, attendanceDate";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fromDate, $toDate);
$stmt->execute();
$result = $stmt->get_result();

$currentClass = '';
$sn = 0;
$hasData = false;

while ($row = $result->fetch_assoc()) {
    $hasData = true;
    
    // Validate that we have a proper attendance date
    if (empty($row['attendanceDate']) || $row['attendanceDate'] == '1970-01-01') {
        continue; // Skip invalid records
    }
    // Add class header when class changes
    if ($currentClass !== $row['className']) {
        $currentClass = $row['className'];
        echo "<tr>
            <td colspan='7' style='background: #1a237e; color: white; font-weight: bold; padding: 10px; text-align: left;'>
                Class: " . htmlspecialchars($currentClass) . "
            </td>
        </tr>";
    }
    
    $sn++;
    $rowClass = $sn % 2 == 0 ? 'row-even' : 'row-odd';
    
    echo "<tr class='{$rowClass}'>
        <td>$sn</td>
        <td>" . htmlspecialchars($row['admissionNumber']) . "</td>
        <td style='text-align: left;'>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</td>
        <td>" . htmlspecialchars($row['className']) . "</td>
        <td>" . htmlspecialchars($row['classArmName']) . "</td>        <td>" . date('d/m/Y', strtotime($row['attendanceDate'])) . "</td>
        <td>";
    
    if (!is_null($row['session1_status']) || !is_null($row['session2_status'])) {
        if (!is_null($row['session1_status'])) {
            $s1Status = $row['session1_status'] == '1' ? 'Present' : 'Absent';
            $s1Class = $row['session1_status'] == '1' ? 'status-present' : 'status-absent';
            echo "<span class='status-badge {$s1Class}'>S1: {$s1Status}</span>";
        }
        
        if (!is_null($row['session1_status']) && !is_null($row['session2_status'])) {
            echo "&nbsp;&nbsp;";
        }
        
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

// If no data found, try to get all students for the selected classes to show who hasn't been marked
if (!$hasData && $reportType === 'today') {
    echo "<tr>
        <td colspan='7' style='text-align: center; padding: 15px; background: #fff3cd; color: #856404; font-weight: bold;'>
            No attendance has been taken for today (" . date('d/m/Y', strtotime($fromDate)) . ")
        </td>
    </tr>";
    
    // Get all students to show who needs attendance to be taken
    $studentQuery = "SELECT 
        c.className,
        ca.classArmName,
        s.admissionNumber,
        s.firstName,
        s.lastName
    FROM tblclass c
    INNER JOIN tblstudents s ON c.Id = s.classId
    INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
    ORDER BY c.className, ca.classArmName, s.firstName, s.lastName";
    
    $studentStmt = $conn->prepare($studentQuery);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();
    
    $currentClass = '';
    $sn = 0;
    
    while ($studentRow = $studentResult->fetch_assoc()) {
        // Add class header when class changes
        if ($currentClass !== $studentRow['className']) {
            $currentClass = $studentRow['className'];
            echo "<tr>
                <td colspan='7' style='background: #1a237e; color: white; font-weight: bold; padding: 10px; text-align: left;'>
                    Class: " . htmlspecialchars($currentClass) . " (Attendance Pending)
                </td>
            </tr>";
        }
        
        $sn++;
        $rowClass = $sn % 2 == 0 ? 'row-even' : 'row-odd';
        
        echo "<tr class='{$rowClass}'>
            <td>$sn</td>
            <td>" . htmlspecialchars($studentRow['admissionNumber']) . "</td>
            <td style='text-align: left;'>" . htmlspecialchars($studentRow['firstName'] . ' ' . $studentRow['lastName']) . "</td>
            <td>" . htmlspecialchars($studentRow['className']) . "</td>
            <td>" . htmlspecialchars($studentRow['classArmName']) . "</td>
            <td>" . date('d/m/Y') . "</td>
            <td><span style='color: #ff9800; font-weight: bold;'>Attendance Pending</span></td>
        </tr>";
    }
} else if (!$hasData) {
    echo "<tr>
        <td colspan='7' style='text-align: center; padding: 20px; color: #666; font-style: italic;'>
            No attendance records found for the selected date range: " . date('d/m/Y', strtotime($fromDate)) . 
            ($fromDate === $toDate ? "" : " - " . date('d/m/Y', strtotime($toDate))) . "
        </td>
    </tr>";
}

echo "</tbody></table>";

// Add summary section
echo "<div style='text-align: right; margin-top: 10px; font-size: 10pt; color: #666;'>
        Generated on: " . date('d/m/Y H:i') . "
      </div>";

echo "</body></html>";
?>