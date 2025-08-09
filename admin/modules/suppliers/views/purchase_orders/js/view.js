$(document).ready(function () {
    const { urls, csrfToken, poId } = PO_DETAIL;

    $('#btnSendOrder').on('click', function () {
        Swal.fire({
            title: PO_DETAIL.t.confirm_send_title,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: PO_DETAIL.t.confirm_send_button
        }).then(result => {
            if (result.isConfirmed) {
                const overlay = $('#poBlockingLoader');
                const msg = $('#poLoaderMessage');
                if (overlay.length) { msg.text(PO_DETAIL.t.sending_order || '...'); overlay.removeClass('hidden'); }
                $.post(urls.sendOrder, { id: poId, csrf_token: csrfToken }, function (res) {
                    if (overlay.length) overlay.addClass('hidden');
                    if (res.success) {
                        Swal.fire(PO_DETAIL.t.generic_ok, PO_DETAIL.t.sent_successfully, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire(PO_DETAIL.t.generic_error, res.message, 'error');
                    }
                }, 'json').fail(function(){ if (overlay.length) overlay.addClass('hidden'); Swal.fire(PO_DETAIL.t.generic_error, 'Network error', 'error'); });
            }
        });
    });

    $('#btnReceiveOrder').on('click', function () {
        // Directly navigate to the full receive page (edit of receiving data)
        window.location.href = urls.receivePage;
    });

    $('#btnResendOrder').on('click', function () {
        const overlay = $('#poBlockingLoader');
        const msg = $('#poLoaderMessage');
        if (overlay.length) {
            msg.text(PO_DETAIL.t.resending_email || '...');
            overlay.removeClass('hidden');
        }
        $.post(urls.resendOrder, { id: poId, csrf_token: csrfToken }, function (res) {
            if (overlay.length) overlay.addClass('hidden');
            if (res.success) {
                Swal.fire(PO_DETAIL.t.generic_ok, PO_DETAIL.t.resent_successfully, 'success');
            } else {
                Swal.fire(PO_DETAIL.t.generic_error, res.message, 'error');
            }
        }, 'json').fail(function(){
            if (overlay.length) overlay.addClass('hidden');
            Swal.fire(PO_DETAIL.t.generic_error, 'Network error', 'error');
        });
    });
});