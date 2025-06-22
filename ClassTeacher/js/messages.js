function showNextMessage(messages, index) {
    if (index >= messages.length) return;

    const message = messages[index];

    Swal.fire({
        title: 'Message from Admin',
        html: `
            <div class="text-left">
                <p>${message.message}</p>
                <small class="text-muted">Sent: ${new Date(message.created_at).toLocaleString()}</small>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Mark as Read',
        cancelButtonText: index + 1 < messages.length ? 'Next Message' : 'Close',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mark message as read using AJAX
            $.ajax({
                url: 'markMessageRead.php',
                method: 'POST',
                data: { messageId: message.id },
                success: function(response) {
                    if (index + 1 < messages.length) {
                        showNextMessage(messages, index + 1);
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel && index + 1 < messages.length) {
            showNextMessage(messages, index + 1);
        }
    });
}