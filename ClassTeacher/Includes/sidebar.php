<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar" style="margin-top: 0; padding-top: 0;">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php" style="height: 70px;">
        <div class="sidebar-brand-icon">
          <img src="../QIUTeacher.png" alt="QAIWAN International University">
        </div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item active" style="margin-top: 0;">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li> 
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Students
      </div>
      </li>
       <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2"
          aria-expanded="true" aria-controls="collapseBootstrap2">
          <i class="fas fa-user-graduate"></i>
          <span>Manage Students</span>
        </a>
        <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Students</h6>
            <a class="collapse-item" href="viewStudents.php">View Students</a>
            <!-- <a class="collapse-item" href="#">Assets Type</a> -->
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
      Attendance
      </div>
      </li>
       <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapcon"
          aria-expanded="true" aria-controls="collapseBootstrapcon">
          <i class="fa fa-calendar-alt"></i>
          <span>Manage Attendance</span>
        </a>
        <div id="collapseBootstrapcon" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Attendance</h6>
            <a class="collapse-item" href="index.php">Take Attendance</a>
            <a class="collapse-item" href="viewAttendance.php">View Class Attendance</a>
            <a class="collapse-item" href="viewStudentAttendance.php">View Student Attendance</a>
            <a class="collapse-item" href="absentWarnings.php">Absence Warnings</a>
     
            <!-- <a class="collapse-item" href="addMemberToContLevel.php ">Add Member to Level</a> -->
          </div>
        </div>
      </li>
      
      <hr class="sidebar-divider">
<div class="sidebar-heading">
    Help & Support
</div>

<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHelp"
        aria-expanded="true" aria-controls="collapseHelp">
        <i class="fas fa-envelope"></i>
        <span>Communication</span>
        <?php
        // Add unread messages counter with prepared statement
        $unreadQuery = "SELECT COUNT(*) as unread FROM tblmessages 
                       WHERE receiverId = ? 
                       AND receiverType = 'teacher' 
                       AND isRead = 0";
        $stmt = $conn->prepare($unreadQuery);
        $stmt->bind_param("i", $_SESSION['userId']);
        $stmt->execute();
        $unreadResult = $stmt->get_result();
        $unreadCount = $unreadResult->fetch_assoc()['unread'];
        if($unreadCount > 0):
        ?>
        <span class="badge badge-warning ml-2"><?php echo $unreadCount; ?></span>
        <?php endif; ?>
    </a>
    <div id="collapseHelp" class="collapse" aria-labelledby="headingHelp" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Messages</h6>
            <a class="collapse-item" href="viewMessages.php">Send Messages</a>
            
        </div>
    </div>
</li>

</ul>

