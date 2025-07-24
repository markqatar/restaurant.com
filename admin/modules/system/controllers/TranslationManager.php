<?php

class TranslationManager
{
    private static $translations = [];
    private static $currentLang = 'en';

    public static function init($lang = null)
    {
        self::$currentLang = $lang ?? ($_SESSION['language'] ?? get_default_admin_language_from_db());
        self::loadCoreTranslations();
    }

    public static function loadCoreTranslations()
    {
        $lang = self::$currentLang;
        $file = get_setting('base_path') . "admin/languages/{$lang}.php";
        if (file_exists($file)) {
            self::$translations = include $file;
        }
    }

    public static function loadModuleTranslations($moduleName)
    {
        $lang = self::$currentLang;
        $file = get_setting('base_path') . "admin/modules/{$moduleName}/languages/{$lang}.php";
        if (file_exists($file)) {
            $moduleTranslations = include $file;
            self::$translations = array_merge(self::$translations, $moduleTranslations);
        }
    }

    public static function t($key)
    {
        $keys = explode('.', $key);
        $value = self::$translations;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // se non trova, restituisce la chiave
            }
        }

        return is_string($value) ? $value : $key;
    }
    /** ✅ Formattazione data in base a setting DB */
    public static function format_date_localized($date, $context = 'admin')
    {
        $format = get_setting($context === 'admin' ? 'date_format_admin' : 'date_format_public', 'd/m/Y H:i');
        $timestamp = strtotime($date);
        if (!$timestamp) return $date;
        return date($format, $timestamp);
    }
}
