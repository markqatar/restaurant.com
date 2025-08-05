$(document).ready(function () {
    const { urls, csrfToken, poId } = PO_DETAIL;

    $('#btnSendOrder').on('click', function () {
        Swal.fire({
            title: 'Confermi invio ordine?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Invia'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(urls.sendOrder, { id: poId, csrf_token: csrfToken }, function (res) {
                    if (res.success) {
                        Swal.fire('Inviato', 'Ordine inviato e PDF generato', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Errore', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    $('#btnReceiveOrder').on('click', function () {
        Swal.fire({
            title: 'Segnare come ricevuto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Conferma'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(urls.receiveOrder, { id: poId, csrf_token: csrfToken }, function (res) {
                    if (res.success) {
                        Swal.fire('Completato', 'Ordine segnato come ricevuto', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Errore', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});