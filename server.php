<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files if they exist in /public
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && is_file(__DIR__.'/public'.$uri)) {
    return false;
}

// Serve static files if they exist in root
if ($uri !== '/' && file_exists(__DIR__.$uri) && is_file(__DIR__.$uri)) {
    return false;
}

// Route all dynamic requests to root index.php
require_once __DIR__.'/index.php';
