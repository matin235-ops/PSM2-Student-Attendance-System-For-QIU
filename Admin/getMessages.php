<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$adminId = $_SESSION['userId'];
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

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        ?>
        <div class="message-card <?php echo $row['senderType'] == 'admin' ? 'message-sent' : 'message-received'; ?>" 
             data-message-id="<?php echo $row['messageId']; ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-1">
                            <?php echo htmlspecialchars($row['senderName']); ?>
                            <?php if($row['senderType'] == 'teacher' && !empty($row['teacherClass'])): ?>
                                <span class="teacher-class">(<?php echo htmlspecialchars($row['teacherClass']); ?>)</span>
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
        <?php
    }
} else {
    echo '<div class="alert alert-info">No messages found</div>';
}
?>