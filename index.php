<?php

$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$currentUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$appUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];

require_once __DIR__.'/src/server/helpers/index.php';

requireAllFilesInFolder(__DIR__.'/src/client/components/ui/');

require_once __DIR__ . '/src/server/config.php'; // Load server configurations

require_once __DIR__ . '/vendor/autoload.php'; // Load Composer autoloader

$fiber = new Fiber(function () use ($currentUrl, $scheme, $appUrl) {

    require_once __DIR__.'/src/client/helpers/index.php';

    require_once __DIR__.'/src/server/database/connect.php';

    (new Fiber(function(){
        requireAllFilesInFolder(__DIR__.'/src/server/database/models/');
    }))->start();

    (new Fiber(function(){
        // Set Content Security Policy (CSP) header
        setCSPHeader();

        // Set other security headers
        setSecurityHeaders();
    }))->start();

    $cache_duration = 3600;

    // Get the requested URL
    $requestUrl = $_SERVER['REQUEST_URI'];

    // Define the base directory for your PHP files
    $baseDirectory = __DIR__ . '/src/client/pages/';

    // Define the base directory for public assets
    $publicDirectory = __DIR__ . '/src/client/public/';

    // Remove query string from the URL
    $requestUrlWithoutQuery = strtok($requestUrl, '?');

    (new Fiber(function () use ($publicDirectory, $requestUrlWithoutQuery, $cache_duration) {
        // Check if the requested URL corresponds to a public asset
        servePublicAsset($publicDirectory, $requestUrlWithoutQuery, $cache_duration);
    }))->start();

    $filePath = '';


    // Find the layout file for the requested page
    $layoutFilePath = find_layout_file($requestUrlWithoutQuery);

    // Special handling for the home page
    $filePath = constructFilePath($baseDirectory, $requestUrlWithoutQuery);

    (new Fiber(function () use ($filePath) {
        // Check if the file exists and is a PHP file
        handleMissingFile($filePath);
    }))->start();

    (new Fiber(function () use ($filePath, $layoutFilePath, $cache_duration, $db, $appUrl) {

        // Include the PHP file and handle layout
        includeAndHandleLayout($filePath, $layoutFilePath, $cache_duration, $db);
    }))->start();

    try {
        $db->close();
    } catch (\Throwable $th) {}
});

$fiber->start();
