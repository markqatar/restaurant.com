$(document).ready(function () {
    const { urls, csrfToken, productId, isRawMaterial, translations } = ASSOCIATE_VARS;
    let editing = false;


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

    // Manual add sub-unit branch (adds next level from last existing or from base unit if none)
    $('#addSubUnitBtn').on('click', function(){
        let baseUnitId = $('#unit_id').val();
        if(!baseUnitId){ Swal.fire('Info','Seleziona prima un\'unità principale','info'); return; }
        // Determine deepest level currently
        let deepest = 0; $('#subUnitsContainer .sub-unit-level').each(function(){ const lvl=$(this).data('level'); if(lvl>deepest) deepest=lvl; });
        if(deepest===0){ loadSubUnit(baseUnitId,1); return; }
        // get last selected sub unit id
        let lastSelect = $('#sub_unit_'+deepest);
        let parentId = lastSelect.val() || baseUnitId;
        loadSubUnit(parentId, deepest+1);
    });

    function loadSubUnit(parentId, level) {
        $.post(urls.unitsSelect, { base_unit_id: parentId, csrf_token: csrfToken }, function (data) {
            if (data.length > 0) {
                const selectId = `sub_unit_${level}`;
                const qtyId = `sub_qty_${level}`;
                const html = `
                    <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                        <label>${translations.subUnitLevel.replace(':level', level)}</label>
                        <select id="${selectId}" name="sub_units[]" class="form-control select2" style="width:100%"></select>
                    </div>
                    <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                        <label>${translations.quantityForUnit}</label>
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
                    placeholder: ''
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
        // If supplier select disabled (editing), ensure value submitted via hidden input
        if(editing && $('#supplier_id').prop('disabled')){
            const val = $('#supplier_id').val();
            if($('#supplier_id_hidden').length===0){
                $('<input>').attr({type:'hidden',id:'supplier_id_hidden',name:'supplier_id'}).val(val).appendTo('#associateForm');
            } else { $('#supplier_id_hidden').val(val); }
        }
        $.post(urls.store, $(this).serialize() + `&csrf_token=${csrfToken}`, function (res) {
            if (res.success) {
                table.ajax.reload();
                resetAssociateForm();
            } else {
                Swal.fire('Errore', res.message, 'error');
            }
        }, 'json');
    });

    // ✅ Delete
    $(document).on('click', '.delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: translations.confirmDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: translations.deleteYes
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

    // ✅ Edit association
    $(document).on('click', '.edit', function(){
        const id = $(this).data('id');
        $.get(urls.get + id, function(res){
            if(!res.success){ Swal.fire(translations.error, translations.recordNotFound, 'error'); return; }
            const d = res.data;
            // Populate form
            $('input[name="product_id"]').val(d.product_id);
            // Supplier select2 load
            const supOption = new Option(d.supplier_name, d.supplier_id, true, true);
            $('#supplier_id').append(supOption).trigger('change');
            // Unit select2 load
            const unitOption = new Option(d.unit_name, d.unit_id, true, true);
            $('#unit_id').append(unitOption).trigger('change');
            // Ensure container cleared (change triggers loadSubUnit asynchronously; we'll rebuild below anyway)
            $('#subUnitsContainer').empty();
            $('input[name="quantity"]').val(d.quantity);
            $('input[name="is_active"]').prop('checked', d.is_active == 1);
            // Clear sub units container then rebuild from data
            $('#subUnitsContainer').empty();
            if(d.sub_units && d.sub_units.length){
                // Sort by level
                d.sub_units.sort((a,b)=>a.level-b.level);
                let parentId = d.unit_id;
                let level = 1;
                const rebuild = ()=>{
                    if(level> d.sub_units.length) return;
                    const su = d.sub_units[level-1];
                    // build containers similar to loadSubUnit but static
                    const selectId = `sub_unit_${level}`;
                    const qtyId = `sub_qty_${level}`;
                    const html = `
                        <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                          <label>${translations.subUnitLevel.replace(':level', level)}</label>
                          <select id="${selectId}" name="sub_units[]" class="form-control select2" style="width:100%"></select>
                        </div>
                        <div class="col-md-4 mb-3 sub-unit-level" data-level="${level}">
                          <label>${translations.quantityForUnit}</label>
                          <input type="number" step="0.01" name="sub_quantities[]" id="${qtyId}" class="form-control" required>
                        </div>`;
                    $('#subUnitsContainer').append(html);
                    $(`#${selectId}`).select2({
                        ajax: {
                            url: urls.unitsSelect,
                            type: 'POST',
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ search: params.term, base_unit_id: parentId, csrf_token: csrfToken }),
                            processResults: data => ({ results: data.map(u=>({id:u.id,text:u.name})) })
                        }
                    });
                    const opt = new Option(su.name, su.unit_id, true, true);
                    $(`#${selectId}`).append(opt).trigger('change');
                    $(`#${qtyId}`).val(su.quantity);
                    parentId = su.unit_id;
                    level++;
                    rebuild();
                };
                rebuild();
            }
            // store id for update
            if($('#assocId').length===0){
                $('<input>').attr({type:'hidden',id:'assocId',name:'id'}).appendTo('#associateForm');
            }
            $('#assocId').val(d.id);
            // Lock supplier select
            if(!$('#supplier_id').prop('disabled')){
                $('#supplier_id').prop('disabled', true).addClass('bg-light');
            }
            if($('#supplier_id_hidden').length===0){
                $('<input>').attr({type:'hidden',id:'supplier_id_hidden',name:'supplier_id'}).val(d.supplier_id).appendTo('#associateForm');
            } else { $('#supplier_id_hidden').val(d.supplier_id); }
            editing = true;
            $('#associateSubmitBtn').text('Aggiorna');
            $('#cancelEditBtn').removeClass('d-none');
        },'json');
    });

    // Cancel edit
    $('#cancelEditBtn').on('click', function(){
        resetAssociateForm();
    });

    function resetAssociateForm(){
        editing = false;
        $('#associateForm')[0].reset();
        $('#subUnitsContainer').empty();
        $('#assocId').remove();
        $('#supplier_id_hidden').remove();
        $('#supplier_id').prop('disabled', false).removeClass('bg-light').val(null).trigger('change');
        $('#unit_id').val(null).trigger('change');
        $('#associateSubmitBtn').text('Associa');
        $('#cancelEditBtn').addClass('d-none');
    }
});