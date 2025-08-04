<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/AuthApi.php';

use Luracast\Restler\Restler;
use Luracast\Restler\Explorer;

$r = new Restler();

// ✅ Imposta Base URL
$r->setBaseUrl('/api');

// ✅ Formato JSON
$r->setSupportedFormats('JsonFormat');

// ✅ Autenticazione SOLO per Explorer
$r->addAuthenticationClass('AuthApi', '/explorer');

// ✅ Aggiungi API Explorer
$r->addAPIClass('explorer');

// ✅ Risorse (opzionale per autodocumentazione)
$r->addAPIClass('Resources');

// ✅ Autoregistrazione moduli
$modulesPath = __DIR__ . '/modules';
foreach (glob($modulesPath . '/*/controllers/*.php') as $controllerFile) {
    require_once $controllerFile;
    $className = basename($controllerFile, '.php');
    $r->addAPIClass($className);
}

// ✅ Avvia Restler
$r->handle();