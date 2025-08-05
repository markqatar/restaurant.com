$(document).ready(function () {
    if (!SUPPLIER_PRODUCTS_LINK_VARS) return;

    const { supplierId, urls, csrfToken, translations } = SUPPLIER_PRODUCTS_LINK_VARS;

    // ✅ Initialize DataTable
    const table = $('#supplierProductsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.datatable,
            type: 'POST',
            data: { supplier_id: supplierId, csrf_token: csrfToken }
        },
        columns: [
            { data: 'supplier_name_real' },
            { data: 'sku' }, // 👈 aggiunto
            { data: 'unit_name' },
            { data: 'quantity' },
            { data: 'price' },
            {
                data: 'id',
                render: function (data) {
                    return `
                <button class="btn btn-sm btn-warning edit-btn" data-id="${data}"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i></button>
            `;
                }
            }
        ]
    });

    // ✅ Select2 for Products
    $('#product_id').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: urls.selectProducts,
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term }),
            processResults: data => ({ results: data })
        }
    });

    // ✅ Select2 for Units
    $('#unit_id').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: urls.selectUnits,
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term }),
            processResults: data => ({ results: data })
        }
    });

    // ✅ Add/Edit Supplier Product
    $('#supplierProductForm').on('submit', function (e) {
        e.preventDefault();
        $.post(urls.store, $(this).serialize() + `&csrf_token=${csrfToken}`, function (res) {
            if (res.success) {
                $('#supplierProductModal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire(translations.error, res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Edit Supplier Product
    $(document).on('click', '.edit', function () {
        const id = $(this).data('id');
        $.get(urls.get + id, function (data) {
            if (data.success) {
                $('#spId').val(data.id);
                $('#supplier_name').val(data.supplier_name);
                $('#quantity').val(data.quantity);
                $('#price').val(data.price);
                $('#currency').val(data.currency);

                // Populate Select2 dynamically
                $('#product_id')
                    .append(new Option(data.product_name, data.product_id, true, true))
                    .trigger('change');
                $('#unit_id')
                    .append(new Option(data.unit_name, data.unit_id, true, true))
                    .trigger('change');

                $('#supplierProductModal').modal('show');
            } else {
                Swal.fire(translations.error, data.message, 'error');
            }
        }, 'json');
    });

    // ✅ Delete Supplier Product
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
});