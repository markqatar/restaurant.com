<?php
// Bootstrap file: carica tutte le dipendenze di base

require_once __DIR__ . '/functions.php';
require_once get_setting('base_path', '/var/www/html') . 'includes/session.php';
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/module_loader.php';

// Carica TranslationManager (dal modulo system)
require_once get_setting('base_path', '/var/www/html') . 'admin/modules/system/controllers/TranslationManager.php';

// Inizializza il sistema di traduzioni
TranslationManager::init();

// Carica moduli attivi
$modules = load_active_modules();

// Ordina moduli per dipendenze
$sortedModules = sort_modules_by_dependencies($modules);

// Carica helpers dei moduli
load_module_helpers($sortedModules);

// Registra gli hooks dei moduli
register_module_hooks($sortedModules);