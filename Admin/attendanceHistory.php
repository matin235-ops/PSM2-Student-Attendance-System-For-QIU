<?php  
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="../QIULOGO1.png" rel="icon">
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="../vendor/datatables/rowGroup.dataTables.min.css" rel="stylesheet">
  <style>
    .table-warning {
        animation: highlight 2s infinite;
    }
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        50% { background-color: #ffeeba; }
        100% { background-color: #fff3cd; }
    }
    
    .dtrg-group {
        background-color: #f8f9fc;
        font-weight: bold;
    }
    
    .dtrg-group td {
        padding: 0.5rem 1rem !important;
    }
    
    #classFilter {
        min-width: 200px;
        display: inline-block;
    }

    .badge-success, .badge-danger, .badge-secondary {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 0.5rem;
    }

    .badge-success {
        background-color: #1cc88a;
    }

    .badge-danger {
        background-color: #e74a3b;
    }

    td:nth-child(5), td:nth-child(6) {
        text-align: center;
    }
  </style>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php";?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php";?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Attendance History</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Attendance History</li>
            </ol>

          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Student Attendance</h6>
                  <div>
                    <button type="button" class="btn btn-warning mr-2" data-toggle="modal" data-target="#clearSessionModal">
                      <i class="fas fa-clock"></i> Clear Session Attendance
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAllModal">
                      <i class="fas fa-trash"></i> Delete All Attendance
                    </button>
                  </div>
                </div>
                <div class="table-responsive p-3">
                  <div class="mb-3 d-flex gap-2">
                    <select id="classFilter" class="form-control w-auto mr-2">
                      <option value="">All Classes</option>
                      <?php
                        $classQuery = "SELECT DISTINCT className, tblclass.Id FROM tblclass 
                                      INNER JOIN tblattendance ON tblclass.Id = tblattendance.classId
                                      ORDER BY className";
                        $classResult = $conn->query($classQuery);
                        while($classRow = $classResult->fetch_assoc()) {
                          echo "<option value='".$classRow['Id']."'>".htmlspecialchars($classRow['className'])."</option>";
                        }
                      ?>
                    </select>

                    <select id="subjectFilter" class="form-control w-auto">
                      <option value="">All Subjects</option>
                      <?php
                        $subjectQuery = "SELECT DISTINCT classArmName, tblclassarms.Id FROM tblclassarms 
                                        INNER JOIN tblattendance ON tblclassarms.Id = tblattendance.classArmId
                                        ORDER BY classArmName";
                        $subjectResult = $conn->query($subjectQuery);
                        while($subjectRow = $subjectResult->fetch_assoc()) {
                          echo "<option value='".$subjectRow['Id']."'>".htmlspecialchars($subjectRow['classArmName'])."</option>";
                        }
                      ?>
                    </select>
                  </div>
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Stage & Group</th>
                        <th>Subject</th>
                        <th>Student Name</th>
                        <th>Session 1</th>
                        <th>Session 2</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $query = "SELECT 
                            tblclass.Id as classId,
                            tblclass.className,
                            tblclassarms.Id as subjectId,
                            tblclassarms.classArmName,
                            tblstudents.firstName,
                            tblstudents.lastName,
                            tblstudents.otherName,
                            tblstudents.admissionNumber,
                            DATE(tblattendance.dateTimeTaken) AS dateTaken,
                            MAX(CASE WHEN tblattendance.sessionNumber = 1 THEN tblattendance.status ELSE NULL END) as session1Status,
                            MAX(CASE WHEN tblattendance.sessionNumber = 2 THEN tblattendance.status ELSE NULL END) as session2Status,
                            CASE 
                                WHEN MAX(tblattendance.dateTimeTaken) >= NOW() - INTERVAL 24 HOUR THEN 1 
                                ELSE 0 
                            END as isNew
                        FROM tblattendance
                        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                        INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                        GROUP BY tblclass.className, tblclassarms.classArmName, tblstudents.admissionNumber, dateTaken
                        ORDER BY dateTaken DESC, tblclass.className, tblclassarms.classArmName, tblstudents.firstName";

                        $rs = $conn->query($query);

                        if ($rs === false) {
                            error_log("Query error: " . $conn->error);
                            echo "<div class='alert alert-danger'>Error fetching attendance records: " . $conn->error . "</div>";
                            $num = 0;
                        } else {
                            $num = $rs->num_rows;
                        }

                        $sn = 0;
                        if ($num > 0) { 
                          while ($rows = $rs->fetch_assoc()) {
                            $sn++;
                            // Create badges for session 1 status
                            $s1Status = isset($rows['session1Status']) ? 
                                ($rows['session1Status'] == '1' ? 
                                '<span class="badge badge-success">S1: Present</span>' : 
                                '<span class="badge badge-danger">S1: Absent</span>') : 
                                '<span class="badge badge-secondary">No Data</span>';
                                
                            // Create badges for session 2 status
                            $s2Status = isset($rows['session2Status']) ? 
                                ($rows['session2Status'] == '1' ? 
                                '<span class="badge badge-success">S2: Present</span>' : 
                                '<span class="badge badge-danger">S2: Absent</span>') : 
                                '<span class="badge badge-secondary">No Data</span>';
                                
                            $newBadge = $rows['isNew'] ? 
                                '<span class="badge badge-warning ml-2">NEW</span>' : 
                                '';
                                
                            echo "
                                <tr" . ($rows['isNew'] ? ' class="table-warning"' : '') . ">
                                    <td>{$sn}</td>
                                    <td>".htmlspecialchars($rows['className'])."</td>
                                    <td>".htmlspecialchars($rows['classArmName'])."</td>
                                    <td>".htmlspecialchars($rows['firstName']).' '.htmlspecialchars($rows['lastName']).' '.htmlspecialchars($rows['otherName']).$newBadge."</td>
                                    <td>{$s1Status}</td>
                                    <td>{$s2Status}</td>
                                    <td>".htmlspecialchars($rows['dateTaken'])."</td>
                                </tr>";
                          }
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php";?>
    </div>
  </div>

  <!-- Clear Session Modal -->
  <div class="modal fade" id="clearSessionModal" tabindex="-1" role="dialog" aria-labelledby="clearSessionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="clearSessionLabel">Clear Session Attendance</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to clear the current session attendance records? This will only remove today's attendance.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="confirmClearSession" class="btn btn-warning">Clear Session</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete All Modal -->
  <div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteAllLabel">Delete All Attendance Records</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Warning: This will permanently delete ALL attendance records. This action cannot be undone!
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" id="confirmDeleteAll" class="btn btn-danger">Delete All</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="../vendor/datatables/dataTables.rowGroup.min.js"></script>

  <script>
    $(document).ready(function () {
        // Update the DataTables initialization
        var table = $('#dataTableHover').DataTable({
            order: [[6, 'desc'], [1, 'asc']], // Date is now column 6
            rowGroup: {
                dataSrc: [1, 2], // Group by class name and subject
                startRender: function(rows, group) {
                    return group + ' (' + rows.count() + ' records)';
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });

        // Class filter functionality
        $('#classFilter').on('change', function() {
            var selectedClass = $(this).val();
            table.column(1)
                .search(selectedClass ? $(this).find('option:selected').text() : '')
                .draw();
        });

        // Subject filter functionality
        $('#subjectFilter').on('change', function() {
            var selectedSubject = $(this).val();
            table.column(2)
                .search(selectedSubject ? $(this).find('option:selected').text() : '')
                .draw();
        });

        // Add CSS for filter layout
        $('<style>')
            .text(`
                .d-flex.gap-2 {
                    display: flex;
                    gap: 0.5rem;
                }
                #classFilter, #subjectFilter {
                    min-width: 200px;
                }
            `)
            .appendTo('head');

        // ... rest of your existing code ...
        $("#confirmClearSession").click(function () {
            $.ajax({
                url: "deleteSessionAttendance.php",
                type: "POST",
                data: { clearSession: true },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert("Today's attendance records cleared successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            });
        });

        $("#confirmDeleteAll").click(function () {
            $.ajax({
                url: "deleteSessionAttendance.php",
                type: "POST",
                data: { deleteAll: true },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert("All attendance records deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            });
        });
    });
  </script>
</body>
</html>
