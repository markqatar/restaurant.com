$(document).ready(function () {
    const { urls, csrfToken } = CREATE_PO_VARS;

    // Init Select2 for Supplier
    $('#supplier_id').select2({
        ajax: {
            url: urls.suppliersSelect,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term, csrf_token: csrfToken }),
            processResults: data => ({ results: data.map(s => ({ id: s.id, text: s.name })) })
        },
        placeholder: 'Seleziona Fornitore'
    });

    // Function to create a new row
    function createRow() {
        return `
            <tr>
                <td>
                    <select name="products[]" class="form-control select2 product-select" required></select>
                </td>
                <td>
                    <input type="number" name="quantities[]" class="form-control" step="0.01" required>
                </td>
                <td>
                    <select name="units[]" class="form-control select2 unit-select" required></select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
    }

    // Add new row
    $('#addItem').on('click', function () {
        $('#orderItemsTable tbody').append(createRow());

        // Init Select2 on new row
        $('.product-select').last().select2({
            ajax: {
                url: urls.productsSelect,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term, csrf_token: csrfToken }),
                processResults: data => ({ results: data.map(p => ({ id: p.id, text: p.name })) })
            },
            placeholder: 'Seleziona Prodotto'
        });

        $('.unit-select').last().select2({
            ajax: {
                url: urls.unitsSelect,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term, csrf_token: csrfToken }),
                processResults: data => ({ results: data.map(u => ({ id: u.id, text: u.name })) })
            },
            placeholder: 'Unità di misura'
        });
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    // Submit PO form
    $('#purchaseOrderForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: urls.store,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire('Salvato', 'Ordine salvato come bozza', 'success')
                        .then(() => window.location.href = '/admin/suppliers/purchaseorders');
                } else {
                    Swal.fire('Errore', res.message || 'Errore sconosciuto', 'error');
                }
            }
        });
    });
});