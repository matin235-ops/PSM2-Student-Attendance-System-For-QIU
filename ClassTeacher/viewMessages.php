<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get all messages for teacher
$teacherId = $_SESSION['userId'];
$query = "SELECT m.*, m.id as messageId, m.isRead,
          CASE 
            WHEN m.senderType = 'admin' THEN 'Admin'
            WHEN m.senderType = 'teacher' THEN 'You'
          END as senderName
          FROM tblmessages m
          WHERE (m.receiverId = ? AND m.receiverType = 'teacher')
          OR (m.senderId = ? AND m.senderType = 'teacher')
          ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $teacherId, $teacherId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../QIULOGO1.png" rel="icon">
    <title>Messages | Teacher Dashboard</title>
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
        .admin-badge {
            background: #4e73df;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        .message-time {
            color: #858796;
            font-size: 0.85rem;
            margin-top: 10px;
        }
        .delete-message {
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        .delete-message:hover {
            opacity: 1;
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            background: #f8f9fc;
            border-radius: 15px 15px 0 0;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn {
            border-radius: 10px;
            padding: 8px 20px;
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
                    <div class="alert alert-primary alert-dismissible fade show mb-4" role="alert" style="background-color: #FF0000FF; border-color: #b8daff; color: #004085;">
                        <i class="fas fa-info-circle" style="color: #004085;"></i>
                        <strong>Need Help?</strong> If you experience any issues with the system, please contact the administrator or send a message through this platform.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Messages</h1>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newMessageModal">
                            <i class="fas fa-paper-plane"></i> Message Admin
                        </button>
                    </div>

                    <div class="messages-container">
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="message-card <?php echo $row['senderType'] == 'teacher' ? 'message-sent' : 'message-received'; ?>" 
                                     data-message-id="<?php echo $row['messageId']; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <?php echo htmlspecialchars($row['senderName']); ?>
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
                    <h5 class="modal-title">Send Message to Admin</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form id="messageForm">
                    <div class="modal-body">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        const REFRESH_INTERVAL = 30000; // 30 seconds
        let refreshTimer;

        function init() {
            setupEventHandlers();
            startAutoRefresh();
        }

        function setupEventHandlers() {
            $('#messageForm').off('submit').on('submit', handleMessageSubmit);
            $(document).off('click', '.delete-message').on('click', '.delete-message', handleMessageDelete);
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
                dataType: 'json',
                cache: false,
                success: function(response) {
                    if (response.success) {
                        let messagesHtml = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(msg) {
                                messagesHtml += `
                                    <div class="message-card ${msg.senderType == 'teacher' ? 'message-sent' : 'message-received'}" 
                                         data-message-id="${msg.messageId}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title mb-1">
                                                        ${escapeHtml(msg.senderName)}
                                                    </h5>
                                                    <div class="message-content mt-2">
                                                        ${escapeHtml(msg.message)}
                                                    </div>
                                                </div>
                                                <button class="btn btn-danger btn-sm delete-message" data-id="${msg.messageId}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="message-time mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    ${new Date(msg.created_at).toLocaleString()}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            messagesHtml = '<div class="alert alert-info">No messages found</div>';
                        }
                        $('.messages-container').html(messagesHtml);
                    } else {
                        console.error('Error loading messages:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                }
            });
        }

        // Add this helper function
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
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
                        url: 'deleteMessage.php',
                        method: 'POST',
                        data: {
                            message_id: messageId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                messageCard.fadeOut(300, function() {
                                    $(this).remove();
                                    if($('.message-card').length === 0) {
                                        $('.messages-container').html('<div class="alert alert-info">No messages found</div>');
                                    }
                                });
                                
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Message has been deleted.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete message', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete message', 'error');
                        }
                    });
                }
            });
        }

        init();
    });
    </script>
</body>
</html>