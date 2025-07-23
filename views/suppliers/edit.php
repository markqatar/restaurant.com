<?php require_once __DIR__ . '/../../admin/includes/header.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Modifica Fornitore
        </h1>
        <a href="suppliers" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Torna all'Elenco
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Modifica: <?php echo htmlspecialchars($supplier['name']); ?></h6>
            <small class="text-muted">ID: <?php echo $supplier['id']; ?></small>
        </div>
        <div class="card-body">
            <form method="POST" action="suppliers/update/<?php echo $supplier['id']; ?>">
                <?php echo csrf_token_field(); ?>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-info-circle"></i> Informazioni Generali
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Fornitore *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Persona di Contatto</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                   value="<?php echo htmlspecialchars($supplier['contact_person'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Indirizzo</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($supplier['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-success">
                            <i class="fas fa-phone"></i> Informazioni di Contatto
                        </h5>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefono</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Geographic Information -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="mb-3 text-info">
                            <i class="fas fa-map-marker-alt"></i> Localizzazione
                        </h5>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="country_id" class="form-label">Paese</label>
                            <select class="form-select select2" id="country_id" name="country_id">
                                <option value="">Seleziona Paese</option>
                                <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['id']; ?>" 
                                        <?php echo ($supplier['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city_id" class="form-label">Città</label>
                            <select class="form-select select2" id="city_id" name="city_id">
                                <?php if ($supplier['city_id'] && $supplier['city_name']): ?>
                                    <option value="<?php echo $supplier['city_id']; ?>" selected>
                                        <?php echo htmlspecialchars($supplier['city_name']); ?>
                                    </option>
                                <?php else: ?>
                                    <option value="">Prima seleziona il paese</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Status -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   <?php echo $supplier['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                <strong>Fornitore Attivo</strong>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salva Modifiche
                    </button>
                    <a href="suppliers" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: function() {
            return $(this).data('placeholder') || 'Seleziona...';
        }
    });
    
    // Country change handler
    $('#country_id').change(function() {
        const countryId = $(this).val();
        const citySelect = $('#city_id');
        
        if (countryId) {
            // Initialize city select with search
            citySelect.select2('destroy');
            citySelect.select2({
                theme: 'bootstrap-5',
                placeholder: 'Cerca e seleziona città...',
                ajax: {
                    url: 'suppliers/get-cities',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            country_id: countryId,
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(city) {
                                return {
                                    id: city.id,
                                    text: city.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
            
            citySelect.prop('disabled', false);
        } else {
            citySelect.empty().append('<option value="">Prima seleziona il paese</option>');
            citySelect.prop('disabled', true);
        }
    });
    
    // Initialize cities if country is already selected
    if ($('#country_id').val() && !$('#city_id').val()) {
        $('#country_id').trigger('change');
    }
});
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>