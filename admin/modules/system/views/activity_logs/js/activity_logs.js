$(document).ready(function () {
    var table = $('#logsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ACTIVITY_LOGS_VARS.datatableUrl,
            type: 'POST'
        },
        columns: [
            { data: 0 }, { data: 1 }, { data: 2 }, { data: 3 }, { data: 4 }, { data: 5 }, { data: 6 }, { data: 7 }
        ]
    });

    $(document).on('click', '.restore-log', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: ACTIVITY_LOGS_VARS.translations.confirmTitle,
            text: ACTIVITY_LOGS_VARS.translations.confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: ACTIVITY_LOGS_VARS.translations.yesRestore,
            cancelButtonText: ACTIVITY_LOGS_VARS.translations.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(ACTIVITY_LOGS_VARS.restoreUrl, {
                    log_id: id
                }, function (response) {
                    Swal.fire(response.success ? ACTIVITY_LOGS_VARS.translations.success : ACTIVITY_LOGS_VARS.translations.error, response.message, response.success ? 'success' : 'error');
                    table.ajax.reload();
                }, 'json');
            }
        });
    });

    $(document).on('click', '.view-details', function () {
        var id = $(this).data('id');
        $.post(ACTIVITY_LOGS_VARS.logDetailsUrl, {
            log_id: id
        }, function (response) {
            if (response.success) {
                $('#logOldData').text(JSON.stringify(response.old_data, null, 2));
                $('#logNewData').text(JSON.stringify(response.new_data, null, 2));
                $('#logDetailsModal').modal('show');
            } else {
                Swal.fire(ACTIVITY_LOGS_VARS.translations.error, response.message, 'error');
            }
        }, 'json');
    });
});