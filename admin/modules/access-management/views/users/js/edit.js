function resetPassword(userId) {
    const { urls, translations } = USERS_ACTIONS_VARS;

    Swal.fire({
        title: translations.confirmResetPassword,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urls.resetPasswordBase + userId;
        }
    });
}

function deleteUser(userId) {
    const { urls, translations } = USERS_ACTIONS_VARS;

    Swal.fire({
        title: translations.confirmDelete,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urls.deleteUserBase + userId;
        }
    });
}