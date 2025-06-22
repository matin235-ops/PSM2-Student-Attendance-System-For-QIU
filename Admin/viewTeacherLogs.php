<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT 
    tl.Id,
    tl.activity,
    tl.logTime,
    CONCAT(t.firstName, ' ', t.lastName) as teacherName,
    tc.class_id,
    c.className,
    DATE_FORMAT(tl.logTime, '%Y-%m-%d') AS logDate,
    DATE_FORMAT(tl.logTime, '%H:%i:%s') AS logTimeOnly
FROM tblteacherlogs tl
LEFT JOIN tblclassteacher t ON tl.teacherId = t.Id
LEFT JOIN teacher_classes tc ON t.Id = tc.teacher_id
LEFT JOIN tblclass c ON tc.class_id = c.Id
WHERE tl.activity LIKE '%attendance%'
ORDER BY tl.logTime DESC";

$rs = $conn->query($query);

if (!$rs) {
    die("Query failed: " . $conn->error);
}

// Store the results in an array instead of using fetch_all
$logs = array();
while ($row = $rs->fetch_assoc()) {
    $logs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../QIULOGO1.png" rel="icon">
    <?php include 'includes/title.php';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <style>
        .activity-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
            color: white;
        }
        .attendance-badge {
            background-color: #4e73df;
        }
        .profile-badge {
            background-color: #1cc88a;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php"; ?>
                <div class="container-fluid">
                    <div class="card mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Teacher Activity Logs</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Teacher Name</th>
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Activity</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (count($logs) > 0) {
                                            $sn = 0;
                                            foreach ($logs as $row) {
                                                if (preg_match('/Session #(\d+)/', $row['activity'], $sessionMatch)) {
                                                    $sn++;
                                                    echo "<tr>
                                                        <td>{$sn}</td>
                                                        <td>" . htmlspecialchars($row['teacherName']) . "</td>
                                                        <td>" . ($row['className'] ? htmlspecialchars($row['className']) : '-') . "</td>
                                                        <td>";
                                                    
                                                    // Extract subject from activity message
                                                    if (preg_match('/\((.*?)\)/', $row['activity'], $matches)) {
                                                        echo htmlspecialchars($matches[1]);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    
                                                    echo "</td><td>";
                                                    echo "<span class='activity-badge attendance-badge'>";
                                                    // Keep the original activity message format
                                                    echo htmlspecialchars($row['activity']) . "</span></td>
                                                        <td>" . htmlspecialchars($row['logDate']) . "</td>
                                                        <td>" . htmlspecialchars($row['logTimeOnly']) . "</td>
                                                    </tr>";
                                                }
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>No activity logs found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "Includes/footer.php"; ?>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [[5, "desc"], [6, "desc"]], // Updated for new column structure
                "pageLength": 25,
                "language": {
                    "emptyTable": "No activity logs found"
                }
            });
        });
    </script>
</body>
</html>
