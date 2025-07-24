<?php require_once get_setting('base_path', '/var/www/html') . 'admin/includes/header.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-edit me-2"></i>Modifica Fornitore
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Torna all'Elenco
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Modifica: <?php echo htmlspecialchars($supplier['name']); ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo admin_url('suppliers', 'update', $supplier['id']); ?>">
                    <?php echo csrf_token_field(); ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome Fornitore *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Persona di Contatto</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($supplier['contact_person'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Indirizzo</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($supplier['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefono</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country_id" class="form-label">Paese</label>
                            <select class="form-select select2" id="country_id" name="country_id">
                                <option value="">Seleziona Paese</option>
                                <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['id']; ?>" <?php echo ($supplier['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="city_id" class="form-label">Città</label>
                            <select class="form-select select2" id="city_id" name="city_id">
                                <?php if ($supplier['city_id'] && $supplier['city_name']): ?>
                                    <option value="<?php echo $supplier['city_id']; ?>" selected><?php echo htmlspecialchars($supplier['city_name']); ?></option>
                                <?php else: ?>
                                    <option value="">Prima seleziona il paese</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo $supplier['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Fornitore Attivo</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Info Fornitore</h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $supplier['id']; ?></p>
                <p><strong>Creato il:</strong> <?php echo format_date_localized($supplier['created_at']); ?></p>
                <?php if ($supplier['updated_at'] && $supplier['updated_at'] != $supplier['created_at']): ?>
                    <p><strong>Ultima modifica:</strong> <?php echo format_date_localized($supplier['updated_at']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>