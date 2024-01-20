<?php

function metaData($title = 'Meta Threads', $url = "", $image = "/screenshot.png", $desc = "A PHP (8.1.10), MySQL and Tailwindcss Meta Threads Application.", $keywords = "PHP, MySQL, Tailwind CSS, Meta Threads", $author = "Shayan") {
    ?>

    <title><?= $title ?></title>

    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $desc ?>">
    <meta name="keywords" content="<?= $keywords ?>">
    <meta name="author" content="<?= $author ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $url ?>">
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:description" content="<?= $desc ?>">
    <meta property="og:image" content="<?= $image ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $url ?>">
    <meta property="twitter:title" content="<?= $title ?>">
    <meta property="twitter:description" content="<?= $desc ?>">
    <meta property="twitter:image" content="<?= $image ?>">

    <!-- Additional Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.ico">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico">

    <!-- Canonical Link -->
    <link rel="canonical" href="<?= $url ?>">
    <?php
}

function buildAttributes($attributes) {
    $result = '';

    foreach ($attributes as $key => $value) {
        $result .= "$key=\"$value\" ";
    }

    return $result;
}

function prettifyNumber($count) {
    if ($count >= 1e12) {
        return round($count / 1e12, 1) . 'T';
    } elseif ($count >= 1e9) {
        return round($count / 1e9, 1) . 'B';
    } elseif ($count >= 1e6) {
        return round($count / 1e6, 1) . 'M';
    } elseif ($count >= 1e3) {
        return round($count / 1e3, 1) . 'K';
    } else {
        return $count;
    }
}

function formatDate(string $timestamp) {
    $timestamp = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $timestamp;
    $seconds = $time_difference;
    $minutes      = round($seconds / 60);           // value 60 is seconds
    $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
    $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 sec
    $weeks        = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
    $months       = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec
    $years        = round($seconds / 31553280);     // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 minutes * 60 sec

    if ($seconds <= 60) {
        return "Just Now";
    } else if ($minutes <= 60) {
        return "$minutes minute" . ($minutes > 1 ? "s" : "") . " ago";
    } else if ($hours <= 24) {
        return "$hours hour" . ($hours > 1 ? "s" : "") . " ago";
    } else if ($days <= 7) {
        return "$days day" . ($days > 1 ? "s" : "") . " ago";
    } else if ($weeks <= 4.3) {  // 4.3 == 30/7
        return "$weeks week" . ($weeks > 1 ? "s" : "") . " ago";
    } else if ($months <= 12) {
        return "$months month" . ($months > 1 ? "s" : "") . " ago";
    } else {
        return "$years year" . ($years > 1 ? "s" : "") . " ago";
    }
}
