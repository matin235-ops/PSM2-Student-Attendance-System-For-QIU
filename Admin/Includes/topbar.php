<?php 
  $query = "SELECT * FROM tbladmin WHERE Id = ".$_SESSION['userId']."";
  $rs = $conn->query($query);
  $num = $rs->num_rows;
  $rows = $rs->fetch_assoc();
  $fullName = $rows['firstName']." ".$rows['lastName'];

?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .navbar.bg-gradient-primary {
        background: #003092 !important; /* Solid color instead of gradient */
    }
    
    /* Update icon colors */
    .navbar .fa-bars,
    .navbar .fa-search,
    .navbar .fa-bell,
    .navbar .fa-user {
        color: #FFF2DB !important;
    }

    /* Update text colors */
    .navbar .text-white {
        color: #FFF2DB !important;
    }

    /* Update dropdown styles */
    .navbar .dropdown-menu {
        border: 1px solid #00879E;
    }

    /* Update notification badge */
    .badge-danger {
        background-color: #FFAB5B !important;
        color: #003092 !important;
    }

    /* Update icon circle in notifications */
    .icon-circle.bg-primary {
        background-color: #00879E !important;
    }

    .dropdown-menu {
        background-color: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
    }

    .dropdown-item {
        border-radius: 6px;
        transition: background-color 0.3s;
    }

    .dropdown-item:hover {
        background-color: #fff5f5;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown-item[href="logout.php"]:hover {
        background-color: #fff5f5;
    }
</style>
</head>
<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top">
          <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
        <div class="text-white big" style="margin-left:100px;"><b></b></div>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-1 small" placeholder="What do you want to look for?"
                      aria-label="Search" aria-describedby="basic-addon2" style="border-color: #25259EFF;">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                      <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
         
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <?php
                    $today = date('Y-m-d');
                    $queryAttendance = "SELECT COUNT(*) as count FROM tblattendance WHERE dateTimeTaken LIKE '$today%'";
                    $rsAttendance = $conn->query($queryAttendance);
                    $attendanceCount = $rsAttendance->fetch_assoc()['count'];
                    ?>
                    <?php if($attendanceCount > 0): ?>
                        <span class="badge badge-danger badge-counter"><?php echo $attendanceCount; ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Attendance Alerts</h6>
                    <a class="dropdown-item d-flex align-items-center" href="attendanceHistory.php" onclick="showAttendanceDetails()">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">Today</div>
                            <span class="font-weight-bold"><?php echo $attendanceCount; ?> new attendance records added</span>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
               
                <i class="fa-solid fa-user" style="color: #000000;"></i>

                <span class="ml-2 d-none d-lg-inline text-white small"><b>Welcome <?php echo $fullName;?></b></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown" style="border-radius: 10px; padding: 10px;">
                <a class="dropdown-item d-flex align-items-center py-2" href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2" style="color: #dc3545;"></i>
                    <span>Logout</span>
                </a>
              </div>
            </li>
          </ul>
        </nav>

<!-- Attendance Details Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Today's Attendance Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="attendanceDetails"></div>
            </div>
        </div>
    </div>
</div>