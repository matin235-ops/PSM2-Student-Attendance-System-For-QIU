<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Attendance_Report.xls"');
header('Cache-Control: max-age=0');

// Create the Excel content
?>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Admission No</th>
            <th>Class</th>
            <th>Section</th>
            <th>Status</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $query = "SELECT 
            tblattendance.status,
            tblattendance.dateTimeTaken,
            tblclass.className,
            tblclassarms.classArmName,
            tblstudents.firstName,
            tblstudents.lastName,
            tblstudents.admissionNumber
        FROM tblattendance
        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
        INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        ORDER BY tblattendance.dateTimeTaken DESC";

        $result = mysqli_query($conn, $query);
        $cnt = 1;

        while($row = mysqli_fetch_assoc($result)) {
            $dateTime = new DateTime($row['dateTimeTaken']);
            ?>
            <tr>
                <td><?php echo $cnt; ?></td>
                <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
                <td><?php echo $row['admissionNumber']; ?></td>
                <td><?php echo $row['className']; ?></td>
                <td><?php echo $row['classArmName']; ?></td>
                <td><?php echo ($row['status'] == 1) ? 'Present' : 'Absent'; ?></td>
                <td><?php echo $dateTime->format('Y-m-d'); ?></td>
                <td><?php echo $dateTime->format('H:i:s'); ?></td>
            </tr>
            <?php 
            $cnt++;
        }
        ?>
    </tbody>
</table> 