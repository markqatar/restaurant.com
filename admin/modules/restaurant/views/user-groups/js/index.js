function deleteGroup(userId, titleText, confirmText, yesText, cancelText) {
    Swal.fire({
        title: titleText,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: yesText,
        cancelButtonText: cancelText
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?php echo admin_url('user-groups', 'delete'); ?>/${userId}`;
        }
    });
}
