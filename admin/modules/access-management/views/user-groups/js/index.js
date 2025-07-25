function deleteGroup(groupId) {
    Swal.fire({
        title: USER_GROUPS_VARS.translations.confirmTitle,
        text: USER_GROUPS_VARS.translations.confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: USER_GROUPS_VARS.translations.yesDelete,
        cancelButtonText: USER_GROUPS_VARS.translations.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `${USER_GROUPS_VARS.deleteUrl}`+groupId;
        }
    });
}