$(document).ready(function () {
    if (!SUPPLIERS_FORM_VARS) return;

    const { urls, translations } = SUPPLIERS_FORM_VARS;

    // Initialize Select2
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

    // Country -> City dependent dropdown
    $('#country_id').change(function () {
        const stateId = $(this).val();
        const citySelect = $('#city_id');

        citySelect.empty().append('<option value="">Caricamento città...</option>').prop('disabled', true);

        if (stateId) {
            // Initialize city select with search
            citySelect.select2('destroy');
            $('#city_id').select2({
                theme: 'bootstrap-5', width: '100%' ,
                ajax: {
                    url: '/admin/system/cities/select2',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            country_id: $('#country_id').val() // ID della country selezionata
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                },
                placeholder: 'Seleziona una città',
                minimumInputLength: 1
            });

            citySelect.prop('disabled', false);
        } else {
            citySelect.empty().append('<option value="">Prima seleziona lo stato</option>');
            citySelect.prop('disabled', true);
        }
    });
});