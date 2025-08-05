$(document).ready(function () {
    const { urls, csrfToken, translations } = SUPPLIER_PRODUCTS_VARS;

    const table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.datatable,
            type: 'POST',
            data: { csrf_token: csrfToken }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'is_raw_material', render: val => val == 1 ? 'Sì' : 'No' },
            { data: 'generate_barcode', render: val => val == 1 ? 'Sì' : 'No' },
            { data: 'requires_expiry', render: val => val == 1 ? 'Sì' : 'No' },
            {
                data: 'id',
                render: data => `
                    <button class="btn btn-sm btn-secondary associate" data-id="${data}">
                        <i class="fas fa-link"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit" data-id="${data}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete" data-id="${data}">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ]
    });

    // ✅ Mostra SKU se Materia Prima è selezionato
    $('#is_raw_material').on('change', function () {
        if ($(this).is(':checked')) {
            $('#skuField').removeClass('d-none');
        } else {
            $('#skuField').addClass('d-none');
            $('#sku').val('');
        }
    });

    // ✅ Add/Edit Product
    $('#productForm').on('submit', function (e) {
        e.preventDefault();
        $.post(urls.store, $(this).serialize() + `&csrf_token=${csrfToken}`, function (res) {
            if (res.success) {
                $('#productModal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire(translations.error, res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Edit Product
    $(document).on('click', '.edit', function () {
        const id = $(this).data('id');
        $.get(urls.get + id, function (res) {
            if (res.success) {
                $('#productId').val(res.data.id);
                $('#name').val(res.data.name);
                $('#sku').val(res.data.sku);
                $('#description').val(res.data.description);
                $('#is_raw_material').prop('checked', res.data.is_raw_material == 1);
                $('#generate_barcode').prop('checked', res.data.generate_barcode == 1);
                $('#requires_expiry').prop('checked', res.data.requires_expiry == 1);
                if (res.data.is_raw_material == 1) {
                    $('#skuField').removeClass('d-none');
                } else {
                    $('#skuField').addClass('d-none');
                }
                $('#productModal').modal('show');
            } else {
                Swal.fire(translations.error, res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Delete Product
    $(document).on('click', '.delete', function () {
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
        }).then(result => {
            if (result.isConfirmed) {
                $.post(urls.delete, { id: id, csrf_token: csrfToken }, function (res) {
                    if (res.success) {
                        table.ajax.reload();
                    } else {
                        Swal.fire(translations.error, res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // ✅ Pulsante Associa Fornitori & Units
    $(document).on('click', '.associate', function () {
        const productId = $(this).data('id');
        window.location.href = `/admin/suppliers/products/associate/${productId}`;
    });
});