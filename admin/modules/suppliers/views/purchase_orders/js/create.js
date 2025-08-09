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
                <td class="last-price" data-price="" data-currency="">-</td>
                <td class="last-price-currency">-</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
    }

    // Add new row (require supplier selected)
    $('#addItem').on('click', function () {
        const supplierId = $('#supplier_id').val();
        if (!supplierId) {
            Swal.fire(CREATE_PO_VARS.translations.generic_error, CREATE_PO_VARS.translations.select_supplier_first, 'warning');
            return;
        }

        const $tbody = $('#orderItemsTable tbody');
        $tbody.append(createRow());
        const $newRow = $tbody.find('tr').last();
        const $productSelect = $newRow.find('.product-select');
        const $unitSelect = $newRow.find('.unit-select');

        // Product select (returns unit metadata)
        $productSelect.select2({
            ajax: {
                url: urls.productsSelect,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term, csrf_token: csrfToken, supplier_id: supplierId }),
                processResults: data => ({ results: data.map(p => ({ id: p.id, text: p.text, unit_id: p.unit_id, unit_name: p.unit_name })) })
            },
            placeholder: CREATE_PO_VARS.translations.product_placeholder
        }).on('select2:select', function (e) {
            const d = e.params.data;
            if (d.unit_id) {
                // lock unit select with the supplier's unit
                $unitSelect.html('').append(new Option(d.unit_name || d.unit_id, d.unit_id, true, true));
                // Keep select enabled for posting OR add hidden input if disabling
                $unitSelect.prop('disabled', true); // disabled removed from POST, so add hidden
                if (!$unitSelect.next('input.unit-hidden').length) {
                    $('<input>', { type: 'hidden', class: 'unit-hidden', name: 'units[]', value: d.unit_id }).insertAfter($unitSelect);
                } else {
                    $unitSelect.next('input.unit-hidden').val(d.unit_id);
                }
            }
        });

        // Unit select initially empty/read-only until product chosen
    $unitSelect.select2({ disabled: true, placeholder: CREATE_PO_VARS.translations.unit_placeholder });
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    // Submit PO form
    $('#purchaseOrderForm').on('submit', function (e) {
        e.preventDefault();
        // Remove last price display cells from POST (they are for stats only)
        const formData = $(this).serialize();
        $.post(urls.store, formData, function(res){
            if (res.success) {
                Swal.fire('Salvato', 'Ordine salvato come bozza', 'success')
                    .then(() => window.location.href = '/admin/suppliers/purchaseorders');
            } else {
                Swal.fire('Errore', res.message || 'Errore sconosciuto', 'error');
            }
        }, 'json');
    });

    // Fetch last price when product selected
    $(document).on('select2:select', '.product-select', function(e){
        const supplierId = $('#supplier_id').val();
        const productId = e.params.data.id;
        const $row = $(this).closest('tr');
        if(!supplierId || !productId) return;
        $.getJSON(`${CREATE_PO_VARS.urls.lastPrice}?supplier_id=${supplierId}&product_id=${productId}`, function(data){
            if(data && data.success){
                const priceText = data.price !== null ? data.price : '-';
                $row.find('.last-price').text(priceText).attr('title', data.date ? `Last: ${data.date}` : '');
                $row.find('.last-price-currency').text(data.currency || '-');
            } else {
                $row.find('.last-price').text('-').attr('title','');
                $row.find('.last-price-currency').text('-');
            }
        });
    });
});