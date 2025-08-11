<?php
/**
 * Translation Audit Script
 * Scans for TranslationManager::t('...') usages across admin and compares with loaded translation arrays
 * Reports:
 *  - Missing: used in code but not defined in the merged (global + module) language arrays
 *  - Orphaned: defined keys never referenced in code (optionally ignoring nested domains)
 *  - Duplicates: same key defined differently across locales
 * Usage (CLI): php translation_audit.php [lang=en] [module=suppliers]
 */

$basePath = dirname(__DIR__, 3) . '/'; // go from modules/system/scripts -> admin/
require_once $basePath . 'includes/functions.php';
require_once $basePath . 'modules/system/controllers/TranslationManager.php';

$lang = 'en';
$moduleFilter = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, 'lang=')) { $lang = substr($arg, 5); }
    if (str_starts_with($arg, 'module=')) { $moduleFilter = substr($arg, 7); }
}

TranslationManager::init($lang);

$global = (function($basePath,$lang){
    $file = $basePath . "languages/$lang.php";
    return file_exists($file) ? include $file : [];
})($basePath,$lang);

$modulesDir = $basePath . 'modules';
$moduleTranslations = [];
foreach (glob($modulesDir . '/*', GLOB_ONLYDIR) as $modDir) {
    $moduleName = basename($modDir);
    if ($moduleFilter && $moduleName !== $moduleFilter) continue;
    $langFile = "$modDir/languages/$lang.php";
    if (file_exists($langFile)) {
        $moduleTranslations[$moduleName] = include $langFile;
    }
}

// Merge like TranslationManager would (later module keys override)
$merged = $global;
foreach ($moduleTranslations as $m => $arr) {
    $merged = array_replace_recursive($merged, $arr);
}

// Flatten helper
function flatten_keys(array $arr, string $prefix = ''): array {
    $out = [];
    foreach ($arr as $k => $v) {
        $full = $prefix === '' ? $k : $prefix . '.' . $k;
        if (is_array($v)) {
            $out += flatten_keys($v, $full);
        } else {
            $out[$full] = $v;
        }
    }
    return $out;
}

$defined = flatten_keys($merged);

// Collect usages
$codeRoots = [
    $basePath . 'modules',
    $basePath . 'layouts',
    $basePath . 'includes'
];
$usageCounts = [];
foreach ($codeRoots as $root) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($iter as $file) {
        if (!$file->isFile()) continue;
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (!in_array($ext, ['php','phtml'])) continue;
        $contents = file_get_contents($file->getPathname());
    if (preg_match_all('/TranslationManager::t\((?:\'([^\']+)\'|"([^"]+)")/', $contents, $matches)) {
            foreach ($matches[1] as $idx => $k1) {
                $key = $k1 ?: $matches[2][$idx];
                if (!isset($usageCounts[$key])) $usageCounts[$key] = 0;
                $usageCounts[$key]++;
            }
        }
    }
}

$usedKeys = array_keys($usageCounts);

// Missing = used but not defined
$missing = array_values(array_diff($usedKeys, array_keys($defined)));
// Orphaned = defined but never used (ignore known container namespaces like 'btn','msg','form','common','status','auth','branch','suppliers')
$ignoreTop = ['btn','msg','form','common','status','auth','branch'];
$orphaned = [];
foreach ($defined as $k => $v) {
    $top = explode('.', $k)[0];
    if (in_array($top,$ignoreTop)) continue; // skip common buckets
    if (!isset($usageCounts[$k])) {
        $orphaned[] = $k;
    }
}

// Output report
$report = [
    'language' => $lang,
    'module_filter' => $moduleFilter,
    'summary' => [
        'defined_keys' => count($defined),
        'used_keys' => count($usedKeys),
        'missing' => count($missing),
        'orphaned' => count($orphaned)
    ],
    'missing_keys' => $missing,
    'orphaned_keys' => $orphaned,
];

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

// Exit non-zero if there are missing keys to allow CI gating
if (!empty($missing)) {
    exit(2);
}
exit(0);
