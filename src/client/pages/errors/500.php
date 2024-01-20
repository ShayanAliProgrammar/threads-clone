<section class="h-full">
    <div class="flex flex-col items-center justify-center h-full max-w-6xl gap-3 px-2 mx-auto">
        <h1 class="text-4xl font-bold">500 Interal Server Error</h1>
        <p class="text-sm text-muted-foreground"><?= $error ?></p>
        <div class="flex gap-2">
            <a href="/" class="<?= buttonStyles('', 'outline') ?>">Home</a>
            <a href="<?= $currentUrl ?>" class="<?= buttonStyles() ?>">Retry</a>
        </div>
    </div>
</section>