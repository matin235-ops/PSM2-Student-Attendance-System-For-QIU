<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get all messages for admin
$adminId = $_SESSION['userId'];
// Update the query to include isRead field
$query = "SELECT m.*, m.id as messageId, m.isRead,
          CASE 
            WHEN m.senderType = 'teacher' THEN CONCAT(t.firstName, ' ', t.lastName)
            WHEN m.senderType = 'admin' THEN 'You'
            ELSE 'Unknown'
          END as senderName,
          CASE
            WHEN m.senderType = 'teacher' THEN 
                GROUP_CONCAT(DISTINCT CONCAT(c.className, ' - ', ca.classArmName) SEPARATOR ', ')
            ELSE ''
          END as teacherClass
          FROM tblmessages m
          LEFT JOIN tblclassteacher t ON m.senderId = t.Id AND m.senderType = 'teacher'
          LEFT JOIN teacher_classes tc ON t.Id = tc.teacher_id
          LEFT JOIN tblclass c ON tc.class_id = c.Id
          LEFT JOIN tblclassarms ca ON tc.class_arm_id = ca.Id
          WHERE (m.receiverId = ? AND m.receiverType = 'admin')
          OR (m.senderId = ? AND m.senderType = 'admin')
          GROUP BY m.id
          ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $adminId, $adminId);
$stmt->execute();
$result = $stmt->get_result();

// Debug message count
error_log("Number of messages found: " . ($result ? $result->num_rows : 0));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../QIULOGO1.png" rel="icon">
    <title>Messages | Admin Dashboard</title>
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .messages-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 15px;
        }
        .message-card {
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .message-card:hover {
            transform: translateY(-2px);
        }
        .message-sent {
            border-left: 5px solid #4e73df;
            background: linear-gradient(to right, #f8f9fc, #ffffff);
        }
        .message-received {
            border-left: 5px solid #1cc88a;
            background: #ffffff;
        }
        .card-body {
            padding: 20px;
        }
        .sender-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .sender-name {
            font-weight: 600;
            color: #4e73df;
            font-size: 1.1rem;
        }
        .teacher-class {
            font-size: 0.85rem;
            color: #858796;
            background: #f8f9fc;
            padding: 4px 12px;
            border-radius: 15px;
        }
        .message-content {
            color: #444;
            line-height: 1.6;
            margin: 15px 0;
            font-size: 1rem;
        }
        .message-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            color: #858796;
            font-size: 0.85rem;
        }
        .timestamp {
            display: flex;
            align-items: center;
        }
        .timestamp i {
            margin-right: 5px;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .btn-send {
            background: #4e73df;
            border: none;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        .btn-send:hover {
            background: #2e59d9;
            transform: translateY(-1px);
        }
        .no-messages {
            text-align: center;
            padding: 40px;
            background: #f8f9fc;
            border-radius: 10px;
            color: #858796;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php";?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php";?>
                <div class="container-fluid">
                   
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <h1 class="h3 mb-0 text-gray-800 mr-3">Messages</h1>
                           
                        </div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newMessageModal">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </div>

                    <div class="messages-container">
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="message-card <?php echo $row['senderType'] == 'admin' ? 'message-sent' : 'message-received'; ?>" 
                                     data-message-id="<?php echo $row['messageId']; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <?php echo htmlspecialchars($row['senderName']); ?>
                                                    <?php if($row['senderType'] == 'teacher'): ?>
                                                        <span class="teacher-info">(<?php echo htmlspecialchars($row['teacherClass']); ?>)</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <div class="message-content mt-2">
                                                    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                                </div>
                                            </div>
                                            <button class="btn btn-danger btn-sm delete-message" data-id="<?php echo $row['messageId']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="message-time mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> 
                                                <?php echo date('F j, Y g:i A', strtotime($row['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info">No messages found</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Message to Teacher</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form id="messageForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Teacher</label>
                            <select class="form-control" name="teacherId" required>
                                <option value="">Select Teacher</option>
                                <?php 
                                $teacherQuery = "SELECT DISTINCT t.Id, t.firstName, t.lastName, 
                                                GROUP_CONCAT(DISTINCT CONCAT(c.className, ' - ', ca.classArmName) SEPARATOR ', ') as classes
                                                FROM tblclassteacher t
                                                LEFT JOIN teacher_classes tc ON t.Id = tc.teacher_id
                                                LEFT JOIN tblclass c ON tc.class_id = c.Id
                                                LEFT JOIN tblclassarms ca ON tc.class_arm_id = ca.Id
                                                WHERE t.firstName IS NOT NULL 
                                                AND t.lastName IS NOT NULL
                                                GROUP BY t.Id, t.firstName, t.lastName
                                                ORDER BY t.firstName, t.lastName";
                                
                                $teacherResult = $conn->query($teacherQuery);
                                
                                if ($teacherResult && $teacherResult->num_rows > 0) {
                                    while($teacher = $teacherResult->fetch_assoc()) {
                                        $teacherName = htmlspecialchars($teacher['firstName'] . ' ' . $teacher['lastName']);
                                        $className = !empty($teacher['classes']) ? 
                                                    htmlspecialchars($teacher['classes']) :
                                                    'No Class Assigned';
                                        echo "<option value='" . $teacher['Id'] . "'>";
                                        echo $teacherName . " (" . $className . ")";
                                        echo "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No teachers found</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
$(document).ready(function() {
    const REFRESH_INTERVAL = 30000; // 30 seconds
    let refreshTimer;

    // Initialize page
    init();

    function init() {
        setupEventHandlers();
        startAutoRefresh();
    }

    function setupEventHandlers() {
        // Message form submission
        $('#messageForm').off('submit').on('submit', handleMessageSubmit);
        
        // Delete message handler
        $(document).off('click', '.delete-message').on('click', '.delete-message', handleMessageDelete);
        
        // Clear all messages handler
        $('#clearAllMessages').off('click').on('click', handleClearAllMessages);
    }

    function startAutoRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
        refreshTimer = setInterval(loadMessages, REFRESH_INTERVAL);
    }

    function loadMessages() {
        $.ajax({
            url: 'getMessages.php',
            method: 'GET',
            cache: false,
            success: function(messages) {
                // Only update if content is different
                if($('.messages-container').html() !== messages) {
                    $('.messages-container').html(messages);
                }
            },
            error: function() {
                console.error('Failed to load messages');
            }
        });
    }

    function handleMessageSubmit(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            url: 'sendMessage.php',
            method: 'POST',
            data: form.serialize() + '&action=send',
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                $('#newMessageModal').modal('hide');
                form[0].reset();
                
                // Load messages after a slight delay to ensure server processing
                setTimeout(loadMessages, 500);
                
                Swal.fire({
                    title: 'Success',
                    text: 'Message sent successfully',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Error', 'Failed to send message', 'error');
            },
            complete: function() {
                form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    }

    function handleMessageDelete(e) {
        e.preventDefault();
        const messageId = $(this).data('id');
        const messageCard = $(this).closest('.message-card');
        
        Swal.fire({
            title: 'Delete Message?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete.php',
                    method: 'POST',
                    data: {
                        delete_message: 1,
                        message_id: messageId
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if(data.status === 'success') {
                            messageCard.fadeOut(300, function() {
                                $(this).remove();
                                updateMessagesDisplay();
                            });
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Message deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }
        });
    }

    function handleClearAllMessages(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Clear All Messages?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'clearMessages.php',
                    method: 'POST',
                    success: function() {
                        loadMessages();
                        Swal.fire({
                            title: 'Cleared!',
                            text: 'All messages have been deleted',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    }

    function updateMessagesDisplay() {
        if($('.message-card').length === 0) {
            $('.messages-container').html(`
                <div class="alert alert-info">No messages found</div>
            `);
        }
    }
});
</script>
</body>
</html>