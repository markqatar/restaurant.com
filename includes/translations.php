<?php
// Translation system for Restaurant Management

// Initialize translations array
global $translations;
$translations = [];

// Get current language from session or default to English
function get_current_language() {
    return $_SESSION['language'] ?? 'en';
}

// Load translations for a specific language
function load_translations($lang = null) {
    global $translations;
    
    if ($lang === null) {
        $lang = get_current_language();
    }
    
    if (!isset($translations[$lang])) {
        $translations_file = __DIR__ . "/translations/{$lang}.php";
        if (file_exists($translations_file)) {
            $translations[$lang] = include $translations_file;
        } else {
            // Fallback to English if language file doesn't exist
            $translations[$lang] = include __DIR__ . "/translations/en.php";
        }
    }
    
    return $translations[$lang];
}

// Translation function - enhanced version
function t($key, $lang = null) {
    $lang = $lang ?? get_current_language();
    $translations = load_translations($lang);
    
    // Support nested keys using dot notation (e.g., 'user.name')
    $keys = explode('.', $key);
    $value = $translations;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            // Return the key if translation not found
            return $key;
        }
    }
    
    return is_string($value) ? $value : $key;
}

// Alias for translate function for backward compatibility
function translate($key, $lang = null) {
    return t($key, $lang);
}

// Get all available languages
function get_available_languages() {
    return [
        'en' => ['name' => 'English', 'flag' => 'fas fa-flag-usa'],
        'it' => ['name' => 'Italiano', 'flag' => 'fas fa-flag'],  
        'ar' => ['name' => 'العربية', 'flag' => 'fas fa-flag']
    ];
}

// Check if language is RTL
function is_rtl_language($lang = null) {
    $lang = $lang ?? get_current_language();
    return in_array($lang, ['ar']);
}

// Format date according to language
function format_date_localized($date, $format = null, $lang = null) {
    $lang = $lang ?? get_current_language();
    
    if ($format === null) {
        $format = ($lang === 'it') ? 'd/m/Y H:i' : (($lang === 'ar') ? 'd/m/Y H:i' : 'm/d/Y H:i');
    }
    
    return date($format, strtotime($date));
}
?>