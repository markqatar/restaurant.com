<?php require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> Nuova Filiale
        </h1>
        <a href="branches" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Torna all'Elenco
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informazioni Filiale</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="branches/store">
                <?php echo csrf_token_field(); ?>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-info-circle"></i> Informazioni Generali
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Filiale *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referente" class="form-label">Referente</label>
                            <input type="text" class="form-control" id="referente" name="referente">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Indirizzo</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
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
                                    <input type="email" class="form-control" id="email1" name="email1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email2" class="form-label">Email Secondaria</label>
                                    <input type="email" class="form-control" id="email2" name="email2">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel1" class="form-label">Telefono Principale</label>
                                    <input type="tel" class="form-control" id="tel1" name="tel1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel2" class="form-label">Telefono Secondario</label>
                                    <input type="tel" class="form-control" id="tel2" name="tel2">
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
                            <label for="country_id" class="form-label">Stato/Regione</label>
                            <select class="form-select select2" id="country_id" name="country_id">
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city_id" class="form-label">Città</label>
                            <select class="form-select select2" id="city_id" name="city_id" disabled>
                                <option value="">Prima seleziona lo stato</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Status -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                <strong>Filiale Attiva</strong>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crea Filiale
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

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/shop/views/branches/js/create.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>