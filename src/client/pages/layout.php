<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= metaData($title ?? 'Meta Threads', $currentUrl, $seo_image??'/screenshot.png', $description ?? "A PHP (8.1.10), MySQL and Tailwindcss Meta Threads Application.", (isset($keywords) ? "PHP, MySQL, Tailwind CSS, Meta Threads, " . $keywords : "PHP, MySQL, Tailwind CSS, Meta Threads"), $author ?? "Shayan") ?>

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

<body class="h-screen max-h-screen bg-muted">
    <div id="swup" class="flex flex-wrap h-screen transition-main">
        <?php include "src/client/components/navbar.php"; ?>

        <div class="flex w-full h-[calc(100vh_-_64px)] overflow-hidden">

            <?php include "src/client/components/sidebar.php"; ?>

            <main class="w-full h-full min-w-[300px] bg-background text-foreground rounded-tl-xl">
                <section class="relative w-full h-full p-5 overflow-auto">
                    <page />
                </section>
            </main>

        </div>
    </div>
</body>

</html>