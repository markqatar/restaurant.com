document.addEventListener('DOMContentLoaded', function() {
    if (!LANGUAGE_CONFIG_VARS) return;

    const { deleteUrlBase, translations } = LANGUAGE_CONFIG_VARS;

    document.querySelectorAll('.delete-language').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.getAttribute('data-code');

            Swal.fire({
                title: translations.deleteLanguageTitle,
                text: code,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: translations.deleteButton
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrlBase + code;
                }
            });
        });
    });
});