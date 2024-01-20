<?php
if (!isset($_SESSION['user'])) {
    header('Location: /');
    die();
}
?>

<section>
    <a href="/" class="<?= buttonStyles('absolute md:top-10 md:left-8 left-3 top-5', 'link') ?>">
        <?php chevronLeft('w-4 h-4 group-hover:-translate-x-1 tarnsition-transform duration-200'); ?>
        Back
    </a>

    <div class="container grid py-10 place-items-center">
        <form action="/api/auth/edit-profile" enctype="multipart/form-data" method="POST" autocomplete="off" class="<?= cardStyle('max-w-sm w-full') ?>">
            <div class="<?= cardHeaderStyle('pb-1') ?>">
                <label for="profile_image" class="w-10 h-10 mb-2 overflow-hidden border rounded-md cursor-pointer">
                    <?php Image($_SESSION['user']['profile_image'], 1000, 1000, ['class'=>'object-contain h-full block']) ?>
                </label>

                <input type="file" accept="image/*" id="profile_image" on:change='/js/profile-image.js' name="profile_image" hidden />


                <h1 class="<?= cardTitleStyle() ?>">Profile</h1>
            </div>
            <div class="<?= cardContentStyle('') ?>">
                <div class="flex flex-col items-center justify-center *:w-full w-full h-full gap-4">
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
                        <label for="username" class="<?= labelStyles() ?>">Username</label>
                        <input type="text" class="<?= inputStyles() ?>" name="username" id="username" value="<?= (new Model())->decrypt($_SESSION['user']['username']) ?>">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="email" class="<?= labelStyles() ?>">Email address</label>
                        <input type="text" class="<?= inputStyles() ?>" name="email" id="email" value="<?= (new Model())->decrypt($_SESSION['user']['email']) ?>" readonly>
                    </div>

                    <button class="<?= buttonStyles() ?>" type="submit">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>