<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$today = date('Y-m-d');

$query = "SELECT a.*, c.className, ca.classArmName, s.firstName, s.lastName
          FROM tblattendance a
          INNER JOIN tblclass c ON a.classId = c.Id
          INNER JOIN tblclassarms ca ON a.classArmId = ca.Id
          INNER JOIN tblstudents s ON a.admissionNo = s.admissionNumber
          WHERE DATE(a.dateTimeTaken) = ?
          ORDER BY a.dateTimeTaken DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'] == '1' ? 
            '<span class="badge badge-success">Present</span>' : 
            '<span class="badge badge-danger">Absent</span>';
            
        echo "<tr>
                <td>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</td>
                <td>" . htmlspecialchars($row['className']) . "</td>
                <td>" . htmlspecialchars($row['classArmName']) . "</td>
                <td>{$status}</td>
                <td>" . date('H:i', strtotime($row['dateTimeTaken'])) . "</td>
              </tr>";
    }
    
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-info">No attendance records found for today.</div>';
}