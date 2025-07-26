$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: function () {
            return $(this).data('placeholder') || 'Seleziona...';
        }
    });
    $('#country_id').select2({
        ajax: {
            url: '/admin/system/countries/select2',
            dataType: 'json',
            delay: 250,
            cache: true, // ✅ Abilita cache lato browser
            data: function (params) {
                return {
                    q: params.term || '', // Testo cercato
                    page: params.page || 1 // Paginazione
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            }
        },
        placeholder: 'Seleziona un Paese',
        minimumInputLength: 1,
        language: {
            inputTooShort: function () {
                return "Digita almeno 1 carattere...";
            },
            noResults: function () {
                return "Nessun risultato trovato";
            },
            searching: function () {
                return "Ricerca in corso...";
            },
            loadingMore: function () {
                return "Caricamento altri risultati...";
            }
        }
    });
    // State change handler
    $('#country_id').change(function () {
        const stateId = $(this).val();
        const citySelect = $('#city_id');

        citySelect.empty().append('<option value="">Caricamento città...</option>').prop('disabled', true);

        if (stateId) {
            // Initialize city select with search
            citySelect.select2('destroy');
            $('#city_id').select2({
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
