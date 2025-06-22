<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report Date Selection</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Select Date Range for Attendance Report</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="generateExcel.php">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3">
                            <label>From Date</label>
                            <input type="date" name="fromDate" class="form-control" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>To Date</label>
                            <input type="date" name="toDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="generate" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>