<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= metaData($title ?? 'Error | Meta Threads', $currentUrl) ?>

    <link rel="prefetch" href="/logo.png">

    <link rel="preload" href="/js/qwik.js" as='script'>

    <link rel="preload" href="/font/Poppins-Regular.woff" as='font' type="font/woff" crossorigin="anonymous">
    <link rel="preload" href="/css/styles.css" as='style'>
    <link rel="preload" href="/js/Swup.umd.js" as='script'>
    <link rel="preload" href="/js/swup-head-plugin.umd.js" as='script'>
    <link rel="preload" href="/js/swup-forms-plugin.umd.js" as='script'>

    <link rel="stylesheet" href="/css/styles.css">

    <script src="/js/Swup.umd.js" defer></script>
    <script src="/js/swup-head-plugin.umd.js" defer></script>
    <script src="/js/swup-forms-plugin.umd.js" defer></script>

    <script src="/js/qwik.js" defer></script>
</head>

<body class="h-screen bg-muted">
    <div id="swup" class="h-full transition-main">
        <main class="relative h-full">
            <page />
        </main>
    </div>
</body>

</html>