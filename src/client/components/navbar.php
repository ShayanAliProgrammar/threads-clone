<header class="flex items-center justify-center w-screen h-16">
    <div class="xl:max-w-[98%] max-w-7xl container flex items-center justify-between w-full gap-3">
        <a href="/" class="<?= buttonStyles('!h-max !p-0', 'outline') ?>">
            <?php Image('/logo.png', 100, 90, ['class'=>'max-w-11 max-h-10 rounded-md overflow-hidden']); ?>
            <span class="sr-only">Threads</span>
        </a>

        <a href="/" class="ml-auto mr-1">
            <?= githubIcon('w-7 h-7') ?>
        </a>

        <?php
        if (isset($_SESSION['user'])){
        ?>

            <a href="/auth/profile" class="<?= buttonStyles('!p-0 w-10 h-10 overflow-hidden rounded-md border !bg-background', 'outline') ?>">
                <?php Image($_SESSION['user']['profile_image'], 1000, 1000, ['class'=>'object-cover max-h-10 max-w-10 w-full h-full !object-top']) ?>
            </a>

        <?php
        } else {
        ?>
            <a href="/auth/sign-in" class="<?= buttonStyles() ?>">Sign In</a>
        <?php
        }
        ?>
    </div>
</header>