<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', '../error_log.txt');

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Debugging helper function
function debug($message) {
    error_log("[DEBUG] " . $message);
}

// Add this function at the top of the file after the session include
function checkClassAvailability($conn, $classId, $armId, $teacherId = null) {
    $query = "SELECT teacher_id FROM teacher_classes 
              WHERE class_id = ? AND class_arm_id = ?";
    
    if ($teacherId) {
        $query .= " AND teacher_id != ?";
    }
    
    $stmt = $conn->prepare($query);
    
    if ($teacherId) {
        $stmt->bind_param("iii", $classId, $armId, $teacherId);
    } else {
        $stmt->bind_param("ii", $classId, $armId);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0;
}

// Add this code at the top of your file after session_start()
if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
    $alertType = $_SESSION['status'] === 'success' ? 'success' : 'danger';
    $alertMessage = $_SESSION['message'];
    unset($_SESSION['status']);
    unset($_SESSION['message']);
}

// Process form submission
if(isset($_POST['save'])) {
    // Debug: Log what we received
    debug("Form submitted with data: " . print_r($_POST, true));
    
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $emailAddress = trim($_POST['emailAddress']);
    $phoneNo = trim($_POST['phoneNo']);
    $password = $_POST['password'];
    $dateCreated = date("Y-m-d");
    
    // Validate input
    if(empty($firstName) || empty($lastName) || empty($emailAddress) || empty($phoneNo) || empty($password)) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Please fill in all required fields!</div>";
        debug("Validation failed: missing required fields");
    } else {
        // Hash the password
        $hashedPassword = md5($password);
        
        // Check if email exists using prepared statement
        $stmt = $conn->prepare("SELECT Id FROM tblclassteacher WHERE emailAddress = ?");
        $stmt->bind_param("s", $emailAddress);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address Already Exists!</div>";
            debug("Email already exists: " . $emailAddress);
        } else {
            try {
                $conn->begin_transaction();
                debug("Starting transaction for teacher creation");
                
                // Insert teacher using prepared statement
                $stmt = $conn->prepare("INSERT INTO tblclassteacher (firstName, lastName, emailAddress, password, phoneNo, dateCreated) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $firstName, $lastName, $emailAddress, $hashedPassword, $phoneNo, $dateCreated);
                
                if ($stmt->execute()) {
                    $teacherId = $conn->insert_id;
                    $success = true;
                    debug("Teacher created with ID: " . $teacherId);
                    
                    // Process each selected class if any
                    if(isset($_POST['classes']) && !empty($_POST['classes'])) {
                        debug("Processing " . count($_POST['classes']) . " selected classes");
                        foreach($_POST['classes'] as $class) {
                            if(strpos($class, ':') !== false) {
                                list($classId, $classArmId) = explode(':', $class);
                                debug("Assigning class: $classId, arm: $classArmId");
                                
                                // Insert into teacher_classes table
                                $stmt = $conn->prepare("INSERT INTO teacher_classes (teacher_id, class_id, class_arm_id) VALUES (?, ?, ?)");
                                $stmt->bind_param("iii", $teacherId, $classId, $classArmId);
                                
                                if (!$stmt->execute()) {
                                    debug("Failed to assign class: " . $stmt->error);
                                    $success = false;
                                    break;
                                }
                                
                                // Update class arm status
                                $stmt = $conn->prepare("UPDATE tblclassarms SET isAssigned='1' WHERE Id = ?");
                                $stmt->bind_param("i", $classArmId);
                                
                                if (!$stmt->execute()) {
                                    debug("Failed to update class arm status: " . $stmt->error);
                                    $success = false;
                                    break;
                                }
                            }
                        }
                    } else {
                        debug("No classes selected - teacher created without class assignments");
                    }
                    
                    if ($success) {
                        $conn->commit();
                        debug("Transaction committed successfully");
                        $_SESSION['status'] = 'success';
                        $_SESSION['message'] = 'Teacher created successfully!';
                        header("Location: createClassTeacher.php");
                        exit();
                    } else {
                        throw new Exception("Error occurred while assigning classes");
                    }
                } else {
                    throw new Exception("Error occurred while creating teacher: " . $stmt->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                debug("Transaction rolled back: " . $e->getMessage());
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Update teacher
if(isset($_POST['update'])) {
    try {
        $conn->begin_transaction();
        
        $Id = $_GET['Id'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $phoneNo = $_POST['phoneNo'];
        
        // Update teacher basic information
        $stmt = $conn->prepare("UPDATE tblclassteacher SET firstName=?, lastName=?, emailAddress=?, phoneNo=? WHERE Id=?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $emailAddress, $phoneNo, $Id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update teacher information");
        }
        
        // Update password if provided
        if (!empty($_POST['newPassword'])) {
            $hashedPassword = md5($_POST['newPassword']);
            $stmt = $conn->prepare("UPDATE tblclassteacher SET password=? WHERE Id=?");
            $stmt->bind_param("si", $hashedPassword, $Id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update password");
            }
        }
        
        // First, unassign all current classes
        $stmt = $conn->prepare("DELETE FROM teacher_classes WHERE teacher_id=?");
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        
        // Update class assignments if classes are selected
        if (isset($_POST['classes']) && !empty($_POST['classes'])) {
            foreach($_POST['classes'] as $class) {
                list($classId, $classArmId) = explode(':', $class);
                
                // Check if the class is available
                if (!checkClassAvailability($conn, $classId, $classArmId, $Id)) {
                    throw new Exception("Class is already assigned to another teacher");
                }
                
                // Insert new class assignments
                $stmt = $conn->prepare("INSERT INTO teacher_classes (teacher_id, class_id, class_arm_id) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $Id, $classId, $classArmId);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to assign class");
                }
                
                // Update class arm status
                $stmt = $conn->prepare("UPDATE tblclassarms SET isAssigned='1' WHERE Id=?");
                $stmt->bind_param("i", $classArmId);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update class arm status");
                }
            }
        }
        
        $conn->commit();
        $_SESSION['status'] = "success";
        $_SESSION['message'] = "Teacher updated successfully!";
        header("Location: createClassTeacher.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['status'] = "error";
        $_SESSION['message'] = $e->getMessage();
        header("Location: createClassTeacher.php");
        exit();
    }
}

// Add this after the update teacher section
if(isset($_POST['delete_teacher']) && isset($_POST['teacher_id'])) {
    try {
        $conn->begin_transaction();
        
        $teacherId = $_POST['teacher_id'];
        
        // First, unassign all classes
        $stmt = $conn->prepare("DELETE FROM teacher_classes WHERE teacher_id = ?");
        $stmt->bind_param("i", $teacherId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to remove class assignments");
        }
        
        // Then delete the teacher
        $stmt = $conn->prepare("DELETE FROM tblclassteacher WHERE Id = ?");
        $stmt->bind_param("i", $teacherId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete teacher");
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Teacher deleted successfully']);
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get teacher data for editing
$editTeacher = null;
$assignedClasses = '';
$classArray = [];

if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['Id'])) {
    $teacherId = $_GET['Id'];
    $query = "SELECT ct.*, 
              GROUP_CONCAT(CONCAT(tc.class_id, ':', tc.class_arm_id)) as assigned_classes
              FROM tblclassteacher ct
              LEFT JOIN teacher_classes tc ON ct.Id = tc.teacher_id
              WHERE ct.Id = ?
              GROUP BY ct.Id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editTeacher = $result->fetch_assoc();
    
    // Initialize assigned classes
    $assignedClasses = '';
    $classArray = [];
    
    if ($editTeacher && isset($editTeacher['assigned_classes'])) {
        $assignedClasses = $editTeacher['assigned_classes'] ?? '';
        if (!empty($assignedClasses)) {
            $classArray = explode(',', $assignedClasses);
        }
    }
}

// Process each class only if there are classes to process
if (!empty($classArray)) {
    foreach ($classArray as $class) {
        if (strpos($class, ':') !== false) {
            list($classId, $classArmId) = explode(':', $class);
            // Your logic here...
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" width="device-width, initial-scale=1">
    <link href="../QIULOGO1.png" rel="icon">
    <title>Create Class Teacher</title>
    
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link href="css/custom-alerts.css" rel="stylesheet">
    
    <style>
        .form-group label { font-weight: bold; }
        .class-group { margin-bottom: 1rem; padding: 1rem; border: 1px solid #e3e6f0; border-radius: 0.35rem; }
        .class-group h6 { color: #4e73df; }
        .class-box-container {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 12px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .class-option {
            display: inline-flex;
            align-items: center;
            width: calc(25% - 16px);
            margin: 8px;
            padding: 8px 12px;
            border: 1px solid #e3e6f0;
            border-radius: 4px;
            background-color: #fff;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .class-option:hover {
            background-color: #f8f9fc;
            border-color: #4e73df;
            box-shadow: 0 2px 4px rgba(78,115,223,0.1);
        }

        .class-option.selected {
            background-color: #4e73df0f;
            border-color: #4e73df;
        }

        .class-option.disabled {
            opacity: 0.7;
            background-color: #f8f9fc;
            border-color: #e3e6f0;
            cursor: not-allowed;
        }

        .class-option input[type="checkbox"] {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }

        .class-option label {
            margin-bottom: 0;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .class-option .text-danger {
            font-size: 0.75rem;
            margin-left: 4px;
        }

        /* Custom scrollbar for webkit browsers */
        .class-box-container::-webkit-scrollbar {
            width: 8px;
        }

        .class-box-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .class-box-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .class-box-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Add this for the section header */
        .assign-classes-header {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #4e73df;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .assign-classes-header h6 {
            color: #4e73df;
            margin: 0;
            font-weight: 600;
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
            z-index: 1050;
            display: flex;
            align-items: center;
            min-width: 300px;
        }

        .custom-alert.show {
            transform: translateX(0);
        }

        .custom-alert-success {
            border-left: 4px solid #28a745;
        }

        .custom-alert-danger {
            border-left: 4px solid #dc3545;
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-icon {
            font-size: 1.2em;
        }

        .fa-check-circle {
            color: #28a745;
        }

        .fa-exclamation-circle {
            color: #dc3545;
        }

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            color: #666;
        }

        .alert-close:hover {
            color: #333;
        }

        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .ml-2 {
            margin-left: .5rem;
        }

        .table td {
            vertical-align: middle;
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.875em;
        }

        .alert {
            margin-bottom: 1rem;
            border-radius: 0.35rem;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            color: #fff;
            background-color: #c82333;
            border-color: #bd2130;
        }

        .modal-content {
            border-radius: 0.3rem;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fc;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fc;
        }

        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 4px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
            z-index: 1050;
            display: flex;
            align-items: center;
            min-width: 200px;
        }

        .floating-alert.show {
            transform: translateX(0);
        }

        .floating-alert-success {
            border-left: 4px solid #28a745;
        }

        .floating-alert-danger {
            border-left: 4px solid #dc3545;
        }

        .floating-alert .alert-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .floating-alert i {
            font-size: 1.2em;
        }

        .floating-alert-success i {
            color: #28a745;
        }

        .floating-alert-danger i {
            color: #dc3545;
        }

        .stage-container {
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 15px;
        }

        .stage-header {
            background-color: #4e73df;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            padding: 10px;
        }

        .subject-option {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 4px;
            padding: 12px;
            transition: all 0.2s ease;
        }

        .subject-option:hover:not(.disabled) {
            border-color: #4e73df;
            box-shadow: 0 2px 4px rgba(78,115,223,0.1);
        }

        .subject-option.selected {
            background-color: #4e73df0f;
            border-color: #4e73df;
        }

        .subject-option.disabled {
            opacity: 0.7;
            background-color: #f8f9fc;
            cursor: not-allowed;
        }

        .stage-subjects {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            background-color: #fff;
        }        #stageSelect {
            max-width: 300px;
        }

        /* Search Box Styling */
        #teacherSearch {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #teacherSearch:focus {
            color: #495057;
            background-color: #fff;
            border-color: #5a5c69;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(90, 92, 105, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fc;
            border-color: #d1d3e2;
            color: #5a5c69;
        }

        #searchResults {
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: inline-block;
        }

        /* Table row hover effect for search */
        .table tbody tr:hover {
            background-color: #f8f9fc !important;
        }

        /* Search highlight effect */
        .table tbody tr[style*="background-color"] {
            border-left: 3px solid #4e73df;
        }

        /* Form validation styles */
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
</head>

<body id="page-top">
    <!-- Add this right after the opening body tag -->
    <div id="alertContainer"></div>
    <div id="wrapper">
        <?php include "Includes/sidebar.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php"; ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Create Class Teacher</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($alertType) && isset($alertMessage)): ?>
                                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $alertMessage; ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($statusMsg)): ?>
                                        <?php echo $statusMsg; ?>
                                    <?php endif; ?>
                                    
                                    <form method="post" class="needs-validation" novalidate>
                                        <input type="hidden" name="teacherId" value="">
                                        <?php if($editTeacher) { ?>
                                            <input type="hidden" name="teacherId" value="<?php echo $editTeacher['Id']; ?>">
                                        <?php } ?>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" name="firstName" required 
                                                       value="<?php echo $editTeacher ? $editTeacher['firstName'] : ''; ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" name="lastName" required 
                                                       value="<?php echo $editTeacher ? $editTeacher['lastName'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="emailAddress" required 
                                                       value="<?php echo $editTeacher ? $editTeacher['emailAddress'] : ''; ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Phone Number</label>
                                                <input type="tel" class="form-control" name="phoneNo" required 
                                                       value="<?php echo $editTeacher ? $editTeacher['phoneNo'] : ''; ?>">
                                            </div>
                                        </div>
                                        <?php if($editTeacher) { ?>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Change Password (leave empty to keep current)</label>
                                                    <input type="password" class="form-control" name="newPassword" minlength="6">
                                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if(!$editTeacher) { ?>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>Password</label>
                                                <input type="password" class="form-control" name="password" required>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <!-- Replace the existing class assignment section with this -->
                                        <div class="form-row">
                                            <div class="col-12">
                                                <div class="assign-classes-header">
                                                    <h6 class="font-weight-bold">Assign Classes (Optional)</h6>
                                                    <small class="text-muted">Select stage and subjects - You can create a teacher without assigning classes</small>
                                                </div>
                                                
                                                <!-- Add stage selector -->
                                                <div class="form-group mb-4">
                                                    <label for="stageSelect">Select Stage</label>
                                                    <select class="form-control" id="stageSelect">
                                                        <option value="">Choose a stage...</option>
                                                        <?php
                                                        $stageQuery = "SELECT DISTINCT c.Id, c.className 
                                                                      FROM tblclass c 
                                                                      ORDER BY c.className";
                                                        $stageResult = $conn->query($stageQuery);
                                                        while($stage = $stageResult->fetch_assoc()) {
                                                            echo '<option value="'.$stage['Id'].'">'.$stage['className'].'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Subject container -->
                                                <div id="subjectsContainer">
                                                    <?php
                                                    // Get all stages and their subjects
                                                    $query = "SELECT 
                                                        c.Id as classId,
                                                        c.className as stageName,
                                                        ca.Id as armId,
                                                        ca.classArmName as subjectName,
                                                        COALESCE(t.Id, '') as teacherId,
                                                        CONCAT(COALESCE(t.firstName, ''), ' ', COALESCE(t.lastName, '')) as teacherName
                                                        FROM tblclass c
                                                        INNER JOIN tblclassarms ca ON c.Id = ca.classId
                                                        LEFT JOIN teacher_classes tc ON ca.Id = tc.class_arm_id
                                                        LEFT JOIN tblclassteacher t ON tc.teacher_id = t.Id
                                                        ORDER BY c.className, ca.classArmName";
                                                        
                                                    $result = $conn->query($query);
                                                    $subjects = array();
                                                    
                                                    while($row = $result->fetch_assoc()) {
                                                        $subjects[$row['classId']][] = $row;
                                                    }
                                                    
                                                    foreach($subjects as $classId => $stageSubjects) {
                                                        echo '<div class="stage-subjects" id="stage-'.$classId.'" style="display: none;">';
                                                        echo '<div class="subjects-grid">';
                                                        
                                                        foreach($stageSubjects as $subject) {
                                                            $isChecked = false;
                                                            if ($editTeacher && !empty($editTeacher['assigned_classes'])) {
                                                                $assignedClassesArray = explode(',', $editTeacher['assigned_classes']);
                                                                $currentClass = $classId.':'.$subject['armId'];
                                                                $isChecked = in_array($currentClass, $assignedClassesArray);
                                                            }
                                                            
                                                            $isDisabled = !empty($subject['teacherId']) && 
                                                                        (!$editTeacher || 
                                                                        ($editTeacher && !in_array($classId.':'.$subject['armId'], explode(',', $editTeacher['assigned_classes'] ?? ''))));
                                                            
                                                            echo '<div class="subject-option '.($isDisabled ? 'disabled' : '').($isChecked ? ' selected' : '').'">';
                                                            echo '<div class="custom-control custom-checkbox">';
                                                            echo '<input type="checkbox" class="custom-control-input" 
                                                                  id="subject_'.$classId.'_'.$subject['armId'].'" 
                                                                  name="classes[]" 
                                                                  value="'.$classId.':'.$subject['armId'].'"
                                                                  '.($isChecked ? 'checked' : '').'
                                                                  '.($isDisabled ? 'disabled' : '').'>';
                                                            echo '<label class="custom-control-label" for="subject_'.$classId.'_'.$subject['armId'].'">';
                                                            echo htmlspecialchars($subject['subjectName']);
                                                            echo '</label>';
                                                            
                                                            if ($isDisabled && !empty($subject['teacherName'])) {
                                                                echo '<small class="d-block text-danger mt-1">Assigned to: '.htmlspecialchars(trim($subject['teacherName'])).'</small>';
                                                            }
                                                            
                                                            echo '</div></div>';
                                                        }
                                                        
                                                        echo '</div></div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add this JavaScript to handle stage selection -->
                                        <script>
                                        document.getElementById('stageSelect').addEventListener('change', function() {
                                            // Hide all subject containers
                                            document.querySelectorAll('.stage-subjects').forEach(el => {
                                                el.style.display = 'none';
                                            });
                                            
                                            // Show selected stage's subjects
                                            const selectedStage = this.value;
                                            if(selectedStage) {
                                                const stageSubjects = document.getElementById('stage-' + selectedStage);
                                                if(stageSubjects) {
                                                    stageSubjects.style.display = 'block';
                                                    
                                                    // Smooth scroll to subjects
                                                    stageSubjects.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                                }
                                            }
                                        });
                                        </script>

                                        <button class="btn btn-primary mt-3" type="submit" name="<?php echo $editTeacher ? 'update' : 'save'; ?>">
                                            <?php echo $editTeacher ? 'Update Teacher' : 'Save Teacher'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Teachers List -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Class Teachers</h6>
                                </div>                                <div class="card-body">
                                    <!-- Search Box -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="teacherSearch" placeholder="Search teachers by name, email, phone, or classes...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <span id="searchResults">Showing all teachers</span>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Classes</th>
                                                    <th>Actions</th>  <!-- New column -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = "SELECT ct.*, 
                                                         GROUP_CONCAT(DISTINCT CONCAT(c.className, ' - ', ca.classArmName) 
                                                         SEPARATOR '<br>') as classes
                                                         FROM tblclassteacher ct
                                                         LEFT JOIN teacher_classes tc ON ct.Id = tc.teacher_id
                                                         LEFT JOIN tblclass c ON tc.class_id = c.Id
                                                         LEFT JOIN tblclassarms ca ON tc.class_arm_id = ca.Id
                                                         GROUP BY ct.Id
                                                         ORDER BY ct.firstName, ct.lastName";
                                                $result = $conn->query($query);
                                                while($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>".$row['firstName']." ".$row['lastName']."</td>";
                                                    echo "<td>".$row['emailAddress']."</td>";
                                                    echo "<td>".$row['phoneNo']."</td>";
                                                    echo "<td>".$row['classes']."</td>";
                                                    echo "<td>
                                                            <a href='?action=edit&Id=".$row['Id']."' class='btn btn-sm btn-primary'>
                                                                <i class='fas fa-edit'></i> Edit
                                                            </a>
                                                            <button class='btn btn-sm btn-danger ml-2 delete-teacher' data-id='".$row['Id']."'>
                                                                <i class='fas fa-trash'></i> Delete
                                                            </button>
                                                          </td>";
                                                    echo "</tr>";
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
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this teacher? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple form validation and submission
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');

        // Handle form submission
        form.addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = form.querySelectorAll('input[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showFloatingAlert('Please fill in all required fields', 'danger');
                return false;
            }
            
            // Show loading state
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            submitButton.disabled = true;
            
            // Form will submit normally, reset button after a delay in case of errors
            setTimeout(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 3000);
        });

        // Handle class selection clicks
        document.querySelectorAll('.subject-option').forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            
            if (checkbox) {
                // Update initial selected state
                if (checkbox.checked) {
                    option.classList.add('selected');
                }
                
                // Handle option clicks
                option.addEventListener('click', (e) => {
                    if (!checkbox.disabled && e.target !== checkbox && e.target.tagName !== 'LABEL') {
                        checkbox.checked = !checkbox.checked;
                        option.classList.toggle('selected', checkbox.checked);
                    }
                });
                
                // Handle checkbox change
                checkbox.addEventListener('change', (e) => {
                    if (!checkbox.disabled) {
                        option.classList.toggle('selected', checkbox.checked);
                    }
                });
            }
        });        // Add this optimized delete handler code after the existing DOMContentLoaded event listener
        document.querySelectorAll('.delete-teacher').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const teacherId = this.dataset.id;
                const row = this.closest('tr');
                
                try {
                    // Show loading state on the delete button
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    
                    const formData = new FormData();
                    formData.append('delete_teacher', '1');
                    formData.append('teacher_id', teacherId);
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Animate row removal
                        row.style.transition = 'all 0.3s ease-out';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        
                        setTimeout(() => {
                            row.remove();
                        }, 300);
                        
                        // Show floating notification
                        showFloatingAlert('Teacher deleted successfully', 'success');
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error("Delete Error:", error);
                    showFloatingAlert(error.message, 'danger');
                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash"></i> Delete';
                }
            });        });

        // Teacher search functionality
        const searchInput = document.getElementById('teacherSearch');
        const teacherTable = document.querySelector('.table tbody');
        const searchResults = document.getElementById('searchResults');
        
        if (searchInput && teacherTable) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = teacherTable.querySelectorAll('tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        // Get text content from all cells except the Actions column (last cell)
                        const searchableText = Array.from(cells)
                            .slice(0, -1) // Exclude the last cell (Actions column)
                            .map(cell => cell.textContent.toLowerCase())
                            .join(' ');
                        
                        const isVisible = searchTerm === '' || searchableText.includes(searchTerm);
                        
                        if (isVisible) {
                            row.style.display = '';
                            visibleCount++;
                            // Add highlight effect for search terms
                            if (searchTerm !== '') {
                                row.style.backgroundColor = '#f8f9fc';
                                row.style.transition = 'background-color 0.3s ease';
                            } else {
                                row.style.backgroundColor = '';
                            }
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
                
                // Update search results text
                if (searchTerm === '') {
                    searchResults.textContent = 'Showing all teachers';
                    searchResults.className = 'text-muted';
                } else {
                    if (visibleCount === 0) {
                        searchResults.textContent = 'No teachers found';
                        searchResults.className = 'text-warning';
                    } else if (visibleCount === 1) {
                        searchResults.textContent = '1 teacher found';
                        searchResults.className = 'text-success';
                    } else {
                        searchResults.textContent = `${visibleCount} teachers found`;
                        searchResults.className = 'text-success';
                    }
                }
            });
            
            // Clear search when escape key is pressed
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                    this.blur();
                }
            });
        }

        // Add this function for floating alerts
        function showFloatingAlert(message, type) {
            const alert = document.createElement('div');
            alert.className = `floating-alert floating-alert-${type}`;
            alert.innerHTML = `
                <div class="alert-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(alert);
            
            // Trigger animation
            setTimeout(() => alert.classList.add('show'), 10);
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    });
    </script>
</body>
</html>