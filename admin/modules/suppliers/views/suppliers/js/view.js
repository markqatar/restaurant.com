$(document).ready(function () {
    if (!SUPPLIER_CONTACTS_VARS) return;

    const { supplierId, urls, csrfToken, translations } = SUPPLIER_CONTACTS_VARS;

    // ✅ Initialize DataTable
    const table = $('#contactsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.datatable,
            type: "POST",
            data: {
                supplier_id: supplierId,
                csrf_token: csrfToken
            }
        },
        columns: [
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            {
                data: 'is_primary',
                render: data => data == 1 ? `<span class="badge bg-primary">${translations.primaryBadge}</span>` : ''
            },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // ✅ Add Contact
    $('#addContactForm').on('submit', function (e) {
        e.preventDefault();
        $.post(urls.store, $(this).serialize(), function (response) {
            if (response.success) {
                $('#addContactModal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire({
                    title: translations.error,
                    text: response.message,
                    icon: 'error'
                });
            }
        }, 'json');
    });

    // ✅ Delete Contact
    $('#contactsTable').on('click', '.delete-contact', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: translations.confirmDeleteTitle,
            text: translations.confirmDeleteText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: translations.yesDelete,
            cancelButtonText: translations.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(urls.delete, { contact_id: id, csrf_token: csrfToken }, function (response) {
                    if (response.success) {
                        table.ajax.reload();
                    } else {
                        Swal.fire(translations.error, response.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // ✅ Edit Contact
    $('#contactsTable').on('click', '.edit-contact', function () {
        const id = $(this).data('id');

        $.get(urls.getContact + id, function (response) {
            if (response.success) {
                const modal = $('#addContactModal');

                modal.find('input[name=first_name]').val(response.data.first_name);
                modal.find('input[name=last_name]').val(response.data.last_name);
                modal.find('input[name=email1]').val(response.data.email1);
                modal.find('input[name=email2]').val(response.data.email2);
                modal.find('input[name=tel1]').val(response.data.tel1);
                modal.find('input[name=tel2]').val(response.data.tel2);
                modal.find('textarea[name=notes]').val(response.data.notes);
                modal.find('input[name=is_primary]').prop('checked', response.data.is_primary == 1);

                // Add hidden field for ID
                if (modal.find('input[name=contact_id]').length === 0) {
                    modal.find('form').append('<input type="hidden" name="contact_id">');
                }
                modal.find('input[name=contact_id]').val(id);

                modal.modal('show');
            } else {
                Swal.fire(translations.error, response.message, 'error');
            }
        }, 'json');
    });

    // ✅ View Contact
    $('#contactsTable').on('click', '.view-contact', function () {
        const id = $(this).data('id');

        $.get(SUPPLIER_CONTACTS_VARS.urls.getContact + id, function (response) {
            if (response.success) {
                const modal = $('#viewContactModal');
                modal.find('.modal-title').text(SUPPLIER_CONTACTS_VARS.translations.viewModalTitle);
                modal.find('.modal-body').html(`
                    <div class="col-md-6 mb-2"><strong>Nome:</strong> ${response.data.first_name} ${response.data.last_name}</div>
                    <div class="col-md-6 mb-2"><strong>Email 1:</strong> ${response.data.email1 || 'N/A'}</div>
                    <div class="col-md-6 mb-2"><strong>Email 2:</strong> ${response.data.email2 || 'N/A'}</div>
                    <div class="col-md-6 mb-2"><strong>Telefono 1:</strong> ${response.data.tel1 || 'N/A'}</div>
                    <div class="col-md-6 mb-2"><strong>Telefono 2:</strong> ${response.data.tel2 || 'N/A'}</div>
                    <div class="col-12 mb-2"><strong>Note:</strong><br>${response.data.notes ? response.data.notes.replace(/\n/g, '<br>') : 'N/A'}</div>
            `);
                modal.modal('show');
            } else {
                Swal.fire(SUPPLIER_CONTACTS_VARS.translations.error, response.message, 'error');
            }
        }, 'json');
    });
});