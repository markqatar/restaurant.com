function deleteUser(userId) {
    Swal.fire({
        title: USERS_VARS.translations.confirmTitle,
        text: USERS_VARS.translations.confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: USERS_VARS.translations.yesDelete,
        cancelButtonText: USERS_VARS.translations.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `${USERS_VARS.deleteUrl}`+userId;
        }
    });
}