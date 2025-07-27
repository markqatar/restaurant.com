<?php

/**
 * Module Loader
 * Gestisce caricamento moduli, dipendenze ed estensioni
 */

function load_module_info($moduleName)
{
    $modulePath = get_setting('base_path', '/var/www/html') . "admin/modules/{$moduleName}/config/module.php";

    if (!file_exists($modulePath)) {
        throw new Exception("Il modulo '{$moduleName}' non esiste nel percorso {$modulePath}");
    }

    $moduleInfo = include $modulePath;

    if (!is_array($moduleInfo)) {
        throw new Exception("File module.php non valido per il modulo '{$moduleName}'");
    }

    return $moduleInfo;
}

/**
 * Carica tutti i moduli attivi (es. definiti in DB in futuro)
 */
function get_all_modules()
{
    $modulesDir = get_setting('base_path', '/var/www/html') . 'admin/modules/';
    $modules = [];

    foreach (scandir($modulesDir) as $dir) {
        if ($dir === '.' || $dir === '..') continue;
        if (is_dir($modulesDir . $dir) && file_exists($modulesDir . $dir . '/config/module.php')) {
            $modules[$dir] = load_module_info($dir);
        }
    }

    return $modules;
}

/**
 * Verifica dipendenze di un modulo
 */
function check_module_dependencies($moduleName)
{
    $info = load_module_info($moduleName);
    $dependencies = $info['dependencies'] ?? [];

    foreach ($dependencies as $dep) {
        $depPath = get_setting('base_path', '/var/www/html') . "admin/modules/{$dep}/config/module.php";
        if (!file_exists($depPath)) {
            throw new Exception("Dipendenza mancante: {$dep} richiesta da {$moduleName}");
        }
    }

    return true;
}

/**
 * Recupera eventuale modulo padre (extends)
 */
function get_module_parent($moduleName)
{
    $info = load_module_info($moduleName);
    return $info['extends'] ?? null;
}

/**
 * Carica un modulo con tutte le sue dipendenze ed eventuale estensione
 */
function load_module($moduleName)
{
    // Verifica dipendenze
    check_module_dependencies($moduleName);

    // Verifica se estende un altro modulo
    $parent = get_module_parent($moduleName);
    if ($parent) {
        load_module($parent); // Carica prima il padre
    }

    // Carica modulo
    return load_module_info($moduleName);
}

/**
 * Carica tutti i moduli attivi leggendo i rispettivi file config/module.php
 */
function load_active_modules()
{
    $modulesPath = get_setting('base_path') . 'admin/modules/';
    $activeModules = [];

    foreach (glob($modulesPath . '*/config/module.php') as $moduleFile) {
        $moduleDir = basename(dirname(dirname($moduleFile))); // Nome modulo
        $config = include $moduleFile;
        $activeModules[$moduleDir] = $config;
    }

    return $activeModules;
}

/**
 * Ordina i moduli in base alle dipendenze dichiarate nel module.php
 */
function sort_modules_by_dependencies($modules)
{
    $sorted = [];
    $visited = [];

    $visit = function ($moduleName) use (&$visit, &$modules, &$sorted, &$visited) {
        if (isset($visited[$moduleName])) {
            return;
        }
        $visited[$moduleName] = true;

        $dependencies = $modules[$moduleName]['dependencies'] ?? [];
        foreach ($dependencies as $dependency) {
            if (isset($modules[$dependency])) {
                $visit($dependency);
            }
        }

        $sorted[$moduleName] = $modules[$moduleName];
    };

    foreach (array_keys($modules) as $moduleName) {
        $visit($moduleName);
    }

    return $sorted;
}

/**
 * Registra gli hook dei moduli caricati
 */
function register_module_hooks($modules)
{
    foreach ($modules as $module) {
    if (!empty($module['hooks_files'])) {
        foreach ($module['hooks_files'] as $type => $file) {
            if (file_exists($file)) {
                $hooks = include $file;
                foreach ($hooks as $hook_name => $callback) {
                    HookManager::register($hook_name, $callback);
                }
            }
        }
    }
    }
}

/**
 * Carica automaticamente tutti gli helpers di ciascun modulo
 */
function load_module_helpers($modules)
{
    foreach (array_keys($modules) as $moduleName) {
        $helperPath = get_setting('base_path') . "admin/modules/{$moduleName}/helpers/";
        if (is_dir($helperPath)) {
            foreach (glob($helperPath . '*.php') as $helperFile) {
                require_once $helperFile;
            }
        }
    }
}
