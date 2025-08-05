$(document).ready(function () {
    const { urls, csrfToken, productId, isRawMaterial } = ASSOCIATE_VARS;


    // ✅ Select2 Fornitori
    $('#supplier_id').select2({
        ajax: {
            url: urls.suppliersSelect,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term, csrf_token: csrfToken }),
            processResults: data => ({ results: data.map(s => ({ id: s.id, text: s.name })) })
        }
    });

    // ✅ Select2 Unità Principale
    $('#unit_id').select2({
        ajax: {
            url: urls.unitsSelect,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term, csrf_token: csrfToken }),
            processResults: data => ({ results: data.map(u => ({ id: u.id, text: u.name })) })
        },
        placeholder: 'Seleziona Unità'
    });

    // ✅ Gestione sotto-unità dinamiche
    $('#unit_id').on('select2:select', function (e) {
        const unitId = e.params.data.id;
        $('#subUnitsContainer').empty();
        loadSubUnit(unitId, 1);
    });

    function loadSubUnit(parentId, level) {
        $.post(urls.unitsSelect, { base_unit_id: parentId, csrf_token: csrfToken }, function (data) {
            if (data.length > 0) {
                const selectId = `sub_unit_${level}`;
                const qtyId = `sub_qty_${level}`;
                const html = `
                    <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                        <label>Sotto-unità Livello ${level}</label>
                        <select id="${selectId}" name="sub_units[]" class="form-control select2" style="width:100%"></select>
                    </div>
                    <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                        <label>Quantità per questa Unità</label>
                        <input type="number" step="0.01" name="sub_quantities[]" id="${qtyId}" class="form-control" required>
                    </div>
                `;
                $('#subUnitsContainer').append(html);

                $(`#${selectId}`).select2({
                    ajax: {
                        url: urls.unitsSelect,
                        type: 'POST',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            search: params.term,
                            base_unit_id: parentId,
                            csrf_token: csrfToken
                        }),
                        processResults: data => ({
                            results: data.map(u => ({ id: u.id, text: u.name }))
                        })
                    },
                    placeholder: 'Seleziona sotto-unità'
                });

                // Se seleziono, carico il livello successivo
                $(`#${selectId}`).on('select2:select', function (e) {
                    const selectedId = e.params.data.id;
                    // Rimuovo eventuali livelli successivi
                    $(`#subUnitsContainer .sub-unit-level`).each(function () {
                        if ($(this).data('level') > level) $(this).remove();
                    });
                    loadSubUnit(selectedId, level + 1);
                });
            }
        }, 'json');
    }

    // ✅ DataTable associazioni
    const table = $('#supplierAssociationsTable').DataTable({
        ajax: {
            url: urls.datatable,
            type: 'POST',
            data: { product_id: productId, csrf_token: csrfToken }
        },
        columns: [
            { data: 'supplier_name' },
            { data: 'unit_name' },
            { data: 'quantity' },
            {
                data: 'sub_units',
                render: function (data, type, row) {
                    if (!data || data.length === 0) return '-';
                    return data.map(su => `${su.name} (x${su.quantity})`).join(' → ');
                }

            }, // JSON → stringa lato server
            { data: 'is_active', render: val => val == 1 ? 'Sì' : 'No' },
            {
                data: 'id',
                render: id => `
                    <button class="btn btn-sm btn-primary edit" data-id="${id}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete" data-id="${id}"><i class="fas fa-trash"></i></button>
                `
            }
        ]
    });

    // ✅ Submit form associazione
    $('#associateForm').on('submit', function (e) {
        e.preventDefault();
        $.post(urls.store, $(this).serialize() + `&csrf_token=${csrfToken}`, function (res) {
            if (res.success) {
                $('#associateForm')[0].reset();
                $('#subUnitsContainer').empty();
                table.ajax.reload();
            } else {
                Swal.fire('Errore', res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Delete
    $(document).on('click', '.delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Confermi la cancellazione?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sì, elimina'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(urls.delete, { id, csrf_token: csrfToken }, function (res) {
                    if (res.success) {
                        table.ajax.reload();
                    } else {
                        Swal.fire('Errore', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});