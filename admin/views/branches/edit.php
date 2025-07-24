<?php require_once get_setting('base_path', '/var/www/html') . 'admin/includes/header.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Modifica Filiale
        </h1>
        <div>
            <a href="branches/manage-users/<?php echo $branch['id']; ?>" class="btn btn-info btn-sm">
                <i class="fas fa-users"></i> Gestisci Utenti
            </a>
            <a href="branches" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Torna all'Elenco
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Modifica: <?php echo htmlspecialchars($branch['name']); ?></h6>
            <small class="text-muted">ID: <?php echo $branch['id']; ?></small>
        </div>
        <div class="card-body">
            <form method="POST" action="branches/update/<?php echo $branch['id']; ?>">
                <?php echo csrf_token_field(); ?>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-info-circle"></i> Informazioni Generali
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Filiale *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($branch['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referente" class="form-label">Referente</label>
                            <input type="text" class="form-control" id="referente" name="referente"
                                   value="<?php echo htmlspecialchars($branch['referente'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Indirizzo</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($branch['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-success">
                            <i class="fas fa-phone"></i> Informazioni di Contatto
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email1" class="form-label">Email Principale</label>
                                    <input type="email" class="form-control" id="email1" name="email1"
                                           value="<?php echo htmlspecialchars($branch['email1'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email2" class="form-label">Email Secondaria</label>
                                    <input type="email" class="form-control" id="email2" name="email2"
                                           value="<?php echo htmlspecialchars($branch['email2'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel1" class="form-label">Telefono Principale</label>
                                    <input type="tel" class="form-control" id="tel1" name="tel1"
                                           value="<?php echo htmlspecialchars($branch['tel1'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel2" class="form-label">Telefono Secondario</label>
                                    <input type="tel" class="form-control" id="tel2" name="tel2"
                                           value="<?php echo htmlspecialchars($branch['tel2'] ?? ''); ?>">
                                </div>
                            </div>
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
                            <label for="state_id" class="form-label">Stato/Regione</label>
                            <select class="form-select select2" id="state_id" name="state_id">
                                <option value="">Seleziona Stato</option>
                                <?php foreach ($states as $state): ?>
                                <option value="<?php echo $state['id']; ?>" 
                                        <?php echo ($branch['state_id'] == $state['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city_id" class="form-label">Città</label>
                            <select class="form-select select2" id="city_id" name="city_id">
                                <?php if ($branch['city_id'] && $branch['city_name']): ?>
                                    <option value="<?php echo $branch['city_id']; ?>" selected>
                                        <?php echo htmlspecialchars($branch['city_name']); ?>
                                    </option>
                                <?php else: ?>
                                    <option value="">Prima seleziona lo stato</option>
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
                                   <?php echo $branch['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                <strong>Filiale Attiva</strong>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salva Modifiche
                    </button>
                    <a href="branches" class="btn btn-secondary">
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
    
    // State change handler
    $('#state_id').change(function() {
        const stateId = $(this).val();
        const citySelect = $('#city_id');
        
        if (stateId) {
            // Initialize city select with search
            citySelect.select2('destroy');
            citySelect.select2({
                theme: 'bootstrap-5',
                placeholder: 'Cerca e seleziona città...',
                ajax: {
                    url: 'branches/get-cities',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            state_id: stateId,
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
            citySelect.empty().append('<option value="">Prima seleziona lo stato</option>');
            citySelect.prop('disabled', true);
        }
    });
    
    // Initialize cities if state is already selected
    if ($('#state_id').val() && !$('#city_id').val()) {
        $('#state_id').trigger('change');
    }
});
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>