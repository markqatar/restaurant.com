<?php
// Site configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir = dirname($_SERVER['REQUEST_URI'] ?? '', 2);
$baseDir = $baseDir === '/' ? '' : $baseDir;

define('SITE_URL', $protocol . $host . $baseDir);

// Add site configuration variables here
$siteUrl = SITE_URL;

// Assets paths
$jsPath = $siteUrl . '/admin/assets/js';
$cssPath = $siteUrl . '/admin/assets/css';
$imgPath = $siteUrl . '/admin/assets/images';

// Theme configuration
$theme = 'default';
$adminTheme = 'admin';
?>