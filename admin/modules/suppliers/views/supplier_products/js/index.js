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
            { data: 'supplier_name_real', title: 'Prodotto' },
            { data: 'sku', title: 'SKU' },
            { data: 'unit_name', title: 'Unità' },
            { data: 'quantity', title: 'Qta Unità' },
            { data: 'base_quantity', title: 'x Base' },
            { data: 'category_slug', title: 'Categoria' },
            { data: 'price', title: 'Prezzo' },
            {
                data: 'id',
                render: function (data) {
                    return `
                <button class="btn btn-sm btn-warning edit" data-id="${data}"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger delete" data-id="${data}"><i class="fas fa-trash"></i></button>
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
        // Basic validation
        const bq = parseFloat($('#base_quantity').val()||'1');
        if(isNaN(bq) || bq <= 0){
            Swal.fire('Errore','La quantità base deve essere > 0','error');
            return;
        }
        $.post(urls.store, $(this).serialize() + `&csrf_token=${csrfToken}`, function (res) {
            if (res.success) {
                $('#supplierProductModal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire(translations.error, res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Reset form for new insertion so previous edit data isn't shown
    function resetSupplierProductForm(){
        const form = document.getElementById('supplierProductForm');
        if(form) form.reset();
        $('#spId').val('');
        // Clear dynamic select2 selections and options
        $('#product_id').val(null).trigger('change');
        $('#product_id').find('option').remove();
        $('#unit_id').val(null).trigger('change');
        $('#unit_id').find('option').remove();
        $('#base_quantity').val('1');
        // Leave category blank
        $('#category_id').val('');
    }

    $('#addSupplierProductBtn').on('click', function(){
        resetSupplierProductForm();
    });

    // ✅ Edit Supplier Product
    $(document).on('click', '.edit', function () {
        const id = $(this).data('id');
        $.get(urls.get + id, function (data) {
            if (data.success) {
                const sp = data;
                $('#spId').val(sp.id);
                $('#supplier_name').val(sp.supplier_name);
                $('#quantity').val(sp.quantity);
                $('#base_quantity').val(sp.base_quantity || 1);
                $('#price').val(sp.price);
                $('#currency').val(sp.currency);
                $('#category_id').val(sp.category_id || '');
                $('#product_id').append(new Option(sp.product_name, sp.product_id, true, true)).trigger('change');
                $('#unit_id').append(new Option(sp.unit_name, sp.unit_id, true, true)).trigger('change');
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