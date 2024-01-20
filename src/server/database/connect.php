<?php

$db;

if (!(!!strpos($currentUrl, '.css')) and !(!!strpos($currentUrl, '.js')) and !(!!strpos($currentUrl, '.png')) and !(!!strpos($currentUrl, '.ico'))  and !(!!strpos($currentUrl, '.txt')) and !(!!strpos($currentUrl, 'images')) and !(!!strpos($currentUrl, 'font'))){
    try {
        global $db;
        // Connect to the database
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        $db = $conn;

    } catch (Exception $th) {
        $dbError = true;
    }
    if (isset($dbError)){
        if (!(!!(strpos($currentUrl, '/errors/500')))){
            $error = "Couldn't connect with database please try again.";
            includeAndHandleLayout(__DIR__.'/../../client/pages/errors/500.php', __DIR__.'/../../client/pages/errors/layout.php', 3600, null, ['error'=>$error]);
            die();
            exit;
        }
    }

}
require_once __DIR__."/helpers.php";