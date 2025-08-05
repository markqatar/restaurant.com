$(document).ready(function () {
    let table = $('#unitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/system/units/datatable',
            type: 'POST'
        },
        order: [[0, 'asc']], // ID ASC di default
        columns: [
            { title: 'ID' },
            { title: 'Nome' },
            { title: 'Short Name' },
            { title: 'Fattore' },
            { title: 'Stato' },
            { title: 'Azioni' }
        ]
    });

    $('#addUnit').click(function () {
        $('#unitForm')[0].reset();
        $('#unitId').val('');
        $('#unitModal').modal('show');
    });

    $('#unitsTable').on('click', '.editUnit', function () {
        let id = $(this).data('id');
        $.post('/admin/system/units/get', { id: id }, function (res) {
            if (res.success) {
                $('#unitId').val(res.data.id);
                $('#unitName').val(res.data.name);
                $('#unitShortName').val(res.data.short_name);
                $('#unitFactor').val(res.data.factor);
                $('#unitActive').prop('checked', res.data.is_active == 1);
                $('.translation-field').each(function () {
                    let lang = $(this).data('lang');
                    $(this).val(res.translations[lang] || '');
                });

                // Carica relazioni
                $.post('/admin/system/units/relations', { id: id }, function (r) {
                    $('#mainRelations').empty();
                    $('#subRelations').empty();

                    r.main.forEach(function (item) {
                        $('#mainRelations').append('<li class="list-group-item d-flex justify-content-between">' +
                            item.name + '<button class="btn btn-sm btn-danger removeRelation" data-id="' + item.id + '">&times;</button></li>');
                    });

                    r.sub.forEach(function (item) {
                        $('#subRelations').append('<li class="list-group-item">' + item.name + '</li>');
                    });

                    // Popola la select per aggiungere nuove relazioni
                    $.post('/admin/system/units/availableUnits', { id: id }, function (units) {
                        let select = $('#addMainRelation');
                        select.empty();
                        select.append('<option value="">Seleziona unita</option>');
                        units.forEach(function (u) {
                            select.append('<option value="' + u.id + '">' + u.name + '</option>');
                        });

                    }, 'json');
                }, 'json');
                $('#unitModal').modal('show');
            }
        }, 'json');
    });

    // Aggiungi relazione
    $('#addMainBtn').click(function () {
        let parent_id = $('#unitId').val();
        let child_id = $('#addMainRelation').val();
        if (child_id) {
            $.post('/admin/system/units/saveRelation', { parent_id: parent_id, child_id: child_id }, function (res) {
                if (res.success) {
                    $('.editUnit[data-id="' + parent_id + '"]').click(); // Ricarica
                }
            }, 'json');
        }
    });

    // Elimina relazione
    $(document).on('click', '.removeRelation', function () {
        let relId = $(this).data('id');
        $.post('/admin/system/units/deleteRelation', { id: relId }, function (res) {
            if (res.success) {
                $('#unitModal').modal('hide');
            }
        }, 'json');
    });

    $('#unitForm').submit(function (e) {
        e.preventDefault();
        $.post('/admin/system/units/save', $(this).serialize(), function (res) {
            if (res.success) {
                $('#unitModal').modal('hide');
                table.ajax.reload();
            }
        }, 'json');
    });

    $('#unitsTable').on('click', '.deleteUnit', function () {
        if (confirm('Are you sure?')) {
            $.post('/admin/system/units/delete', { id: $(this).data('id') }, function (res) {
                if (res.success) { table.ajax.reload(); }
            }, 'json');
        }
    });
});