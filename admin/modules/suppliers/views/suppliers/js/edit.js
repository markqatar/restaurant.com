$(document).ready(function () {
    if (!SUPPLIERS_FORM_VARS) return;

    const { urls, translations } = SUPPLIERS_FORM_VARS;

    // ✅ Initialize Select2
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

    // ✅ Country -> City dependent dropdown
    $('#country_id').on('change', function () {
        const countryId = $(this).val();
        const citySelect = $('#city_id');

        citySelect.empty()
            .append(`<option value="">${translations.loading}</option>`)
            .prop('disabled', true);

        if (countryId) {
            if ($.fn.select2 && $('#citySelect').hasClass('select2-hidden-accessible')) {
                $('#citySelect').select2('destroy');
            }

            citySelect.select2({
                theme: 'bootstrap-5',
                placeholder: translations.searchCity,
                ajax: {
                    url: '/admin/system/cities/select2',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        country_id: countryId,
                        q: params.term || ''
                    }),
                    processResults: data => ({
                        results: data.results,
                        pagination: data.pagination
                    }),
                    cache: true
                }
            }).prop('disabled', false);
        } else {
            citySelect.empty()
                .append(`<option value="">${translations.selectCountryFirst}</option>`)
                .prop('disabled', true);
        }
    });

    // ✅ Auto-trigger if country is pre-selected and city is empty
    if ($('#country_id').val() && !$('#city_id').val()) {
        $('#country_id').trigger('change');
    }
});