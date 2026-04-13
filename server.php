<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Check if file exists in public directory
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && is_file(__DIR__.'/public'.$uri)) {
    $file = __DIR__.'/public'.$uri;
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mimes = [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: '.$mimes[$ext]);
    } else {
        header('Content-Type: '.mime_content_type($file));
    }
    readfile($file);
    exit;
}

// Serve static files from root if they exist
if ($uri !== '/' && file_exists(__DIR__.$uri) && is_file(__DIR__.$uri)) {
    return false;
}

// Otherwise, route all requests to index.php
require_once __DIR__.'/index.php';
