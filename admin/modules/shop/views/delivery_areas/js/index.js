function confirmDelete(id, areaName) {
    const { urls, translations } = DELIVERY_AREAS_VARS;

    Swal.fire({
        title: translations.confirmDeleteTitle,
        text: translations.confirmDeleteText.replace('%s', areaName),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: translations.yesDelete,
        cancelButtonText: translations.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urls.deleteBase + id;
        }
    });
}