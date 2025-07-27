<?php

class HookManager
{
    private static $hooks = [];
    private static $logicHooks = [];

    /**
     * Registra un hook UI (rendering HTML)
     */
    public static function registerHook($hookName, callable $callback)
    {
        if (!isset(self::$hooks[$hookName])) {
            self::$hooks[$hookName] = [];
        }
        self::$hooks[$hookName][] = $callback;
    }

    /**
     * Registra un logic hook (esecuzione dopo evento)
     */
    public static function registerLogicHook($eventName, callable $callback)
    {
        if (!isset(self::$logicHooks[$eventName])) {
            self::$logicHooks[$eventName] = [];
        }
        self::$logicHooks[$eventName][] = $callback;
    }

    /**
     * Esegue tutti i callback associati a un hook UI
     */
    public static function executeHook($hookName, ...$args)
    {
        if (isset(self::$hooks[$hookName])) {
            foreach (self::$hooks[$hookName] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Esegue tutti i callback associati a un logic hook
     */
    public static function triggerEvent($eventName, ...$args)
    {
        if (isset(self::$logicHooks[$eventName])) {
            foreach (self::$logicHooks[$eventName] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Carica gli hook definiti nei moduli
     */
    public static function loadModuleHooks()
    {
        $modulesDir = get_setting('base_path') . 'admin/modules/';
        $modules = scandir($modulesDir);

        foreach ($modules as $module) {
            if ($module === '.' || $module === '..') continue;

            $hookFile = $modulesDir . $module . '/hooks/ui_hooks.php';
            $logicFile = $modulesDir . $module . '/hooks/logic_hooks.php';

            if (file_exists($hookFile)) {
                include_once $hookFile;
            }

            if (file_exists($logicFile)) {
                include_once $logicFile;
            }
        }
    }
}