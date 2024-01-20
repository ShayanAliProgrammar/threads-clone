<?php
if (isset($_SESSION['user'])) {
    header('Location: /');
    die();
}

$title = "Sign In - Meta Threads";
?>

<section>
    <a href="/" class="<?= buttonStyles('absolute md:top-10 md:left-8 left-3 top-5', 'link') ?>">
        <?php chevronLeft('w-4 h-4 group-hover:-translate-x-1 tarnsition-transform duration-200'); ?>
        Back
    </a>

    <div class="container grid py-10 place-items-center">
        <div class="<?= cardStyle('max-w-sm w-full') ?>">
            <div class="<?= cardHeaderStyle('') ?>">
                <?php Image('/logo.png', 100, 90, ['class'=>'w-10 h-9'], ['class'=>'w-10 my-2 h-9']) ?>
                <div class="flex flex-col gap-2 pt-3">
                    <h1 class="<?= cardTitleStyle() ?>">Sign In</h1>
                    <p class="<?= cardDescStyle() ?>">to continue to threads</p>
                </div>
            </div>
            <div class="<?= cardContentStyle('') ?>">
                <form action="/api/auth/signin" method="POST" autocomplete="off" class="flex flex-col items-center justify-center *:w-full w-full h-full gap-4">
                <?php
                    if (isset($_GET['errors'])){
                    ?>
                    <ul class="p-2 text-sm rounded bg-destructive text-destructive-foreground">
                        <?php
                        $errors = explode(', ', $_GET['errors']);

                        foreach ($errors as $error) {

                            $error = ucwords(str_replace('_', ' ', $error));
                        ?>

                        <li>
                            <?= $error ?>
                        </li>

                        <?php
                        }
                        ?>

                    </ul>
                    <?php
                    }
                    ?>

                    <div class="flex flex-col gap-2">
                        <label for="email_or_username" class="<?= labelStyles() ?>">Email address or username</label>
                        <input type="text" class="<?= inputStyles() ?>" name="email_or_username" id="email_or_username" value="demo@example.com">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="password" class="<?= labelStyles() ?>">Password</label>
                        <input type="password" class="<?= inputStyles() ?>" name="password" id="password" value="demo">
                    </div>


                    <button class="<?= buttonStyles() ?>">
                        Continue
                    </button>

                    <a href="/auth/sign-up" class="text-xs">No account? <span class="pl-1 hover:text-primary hover:underline">Sign Up</span></a>
                </form>
            </div>
        </div>
    </div>
</section>