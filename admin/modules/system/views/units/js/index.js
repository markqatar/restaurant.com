$(document).ready(function(){
    let table = $('#unitsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/admin/system/units/datatable',
        type: 'POST'
    },
    columns: [
        { title: 'ID' },
        { title: 'Nome' },
        { title: 'Short Name' },
        { title: 'Fattore' },
        { title: 'Tipo' },
        { title: 'Stato' },
        { title: 'Azioni' }
    ]
});

    $('#addUnit').click(function(){
        $('#unitForm')[0].reset();
        $('#unitId').val('');
        $('#unitModal').modal('show');
    });

    $('#unitsTable').on('click','.editUnit',function(){
        let id = $(this).data('id');
        $.post('/admin/system/units/get',{id:id},function(res){
            if(res.success){
                $('#unitId').val(res.data.id);
                $('#unitName').val(res.data.name);
                $('#unitShortName').val(res.data.short_name);
                $('#unitFactor').val(res.data.factor);
                $('#unitType').val(res.data.type);
                $('#unitActive').prop('checked', res.data.is_active==1);
                $('.translation-field').each(function(){
                    let lang = $(this).data('lang');
                    $(this).val(res.translations[lang] || '');
                });
                $('#unitModal').modal('show');
            }
        },'json');
    });

    $('#unitForm').submit(function(e){
        e.preventDefault();
        $.post('/admin/system/units/save', $(this).serialize(), function(res){
            if(res.success){
                $('#unitModal').modal('hide');
                table.ajax.reload();
            }
        },'json');
    });

    $('#unitsTable').on('click','.deleteUnit',function(){
        if(confirm('Are you sure?')){
            $.post('/admin/system/units/delete',{id:$(this).data('id')},function(res){
                if(res.success){ table.ajax.reload(); }
            },'json');
        }
    });
});