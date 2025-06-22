<ul class="navbar-nav sidebar accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
        <img src="../QIUTeacher.png" alt="QAIWAN International University">
        </div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Stages and Subjects
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="fas fa-chalkboard"></i>
            <span>Manage Stages</span>    
        </a>
        <div id="collapseBootstrap" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Manage Stages</h6>
                <a class="collapse-item" href="createClass.php">Create Stage</a>
                <!-- <a class="collapse-item" href="#">Member List</a> -->
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapusers" aria-expanded="true" aria-controls="collapseBootstrapusers">
            <i class="fas fa-code-branch"></i>
            <span>Manage Stages Subjects</span>
        </a>
        <div id="collapseBootstrapusers" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Manage Stages Subjects</h6>
                <a class="collapse-item" href="createClassArms.php">Create Subjects</a>
                
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Teachers
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapassests" aria-expanded="true" aria-controls="collapseBootstrapassests">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Manage Teachers</span>
        </a>
        <div id="collapseBootstrapassests" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Manage Class Teachers</h6>
                <a class="collapse-item" href="createClassTeacher.php">Create Class Teachers</a>
              
            </div>
        </div>
    </li>
    

     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Students
     </div>
     </li>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2" aria-expanded="true" aria-controls="collapseBootstrap2">
             <i class="fas fa-user-graduate"></i>
             <span>Manage Students</span>
         </a>
         <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Manage Students</h6>
                 <a class="collapse-item" href="createStudents.php">Create Students</a>
             
             </div>
         </div>
     </li>

    
     
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Attendance Records
     </div>
     <li class="nav-item">
         <a class="nav-link" href="attendanceHistory.php">
             <i class="fas fa-history"></i>
             <span>Attendance History</span>
         </a>
     </li>

    


    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        <i class="fas fa-chart-line mr-2"></i>Reports & Analytics
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" 
           aria-expanded="true" aria-controls="collapseReports">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="collapseReports" class="collapse" aria-labelledby="headingReports" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Analytics Tools</h6>
                <a class="collapse-item" href="analytics.php">
                    <i class="fas fa-chart-bar fa-sm fa-fw mr-2"></i>Analytics Dashboard
                </a>
                <a class="collapse-item" href="studentAbsenceWarnings.php">
                    <i class="fas fa-exclamation-triangle fa-sm fa-fw mr-2"></i>Absence Warnings
                </a>
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
        // Move unread counter next to Communication text
        $unreadQuery = "SELECT COUNT(*) as unread FROM tblmessages 
                       WHERE receiverId = ? 
                       AND receiverType = 'admin' 
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
            <a class="collapse-item" href="viewMessages.php">View Messages</a>
            
        </div>
    </div>
</li>

</ul>

