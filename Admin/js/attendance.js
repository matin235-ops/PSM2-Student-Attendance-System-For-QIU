function showAttendanceDetails() {
    $('#attendanceModal').modal('show');
    fetchAttendanceDetails();
}

function fetchAttendanceDetails() {
    $.ajax({
        url: 'getAttendanceDetails.php',
        type: 'GET',
        success: function(response) {
            $('#attendanceDetails').html(response);
        },
        error: function() {
            $('#attendanceDetails').html('<div class="alert alert-danger">Error fetching attendance details</div>');
        }
    });
}

function deleteAttendance() {
    if (confirm("Are you absolutely sure you want to delete ALL attendance records? This cannot be undone!")) {
        $.ajax({
            url: 'deleteSessionAttendance.php',
            type: 'POST',
            data: { deleteAll: true },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('All attendance records deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Unknown error occurred'));
                    }
                } catch (e) {
                    alert('Error processing server response');
                }
            },
            error: function() {
                alert('Error connecting to the server. Please try again.');
            }
        });
    }
}