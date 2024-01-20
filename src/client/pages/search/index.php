<?php

$title = "Search Users | Meta Threads";

if (!isset($_SESSION['user'])){
    header('Location: /auth/sign-in');
    die();
}

$userModel = new User($db);

// Get the users and display them
$usersPerPage = 5;
$totalUsers = count($userModel->getAllUsers());
$totalPages = ceil($totalUsers / $usersPerPage);
$page = isset($_GET['page']) ? max(1, min($_GET['page'], $totalPages)) : 1;
$startIndex = ($page - 1) * $usersPerPage;

$search = isset($_GET['user-search']) ? strtolower($_GET['user-search']) : '';

?>
<h1 class="mb-4 text-3xl font-bold">Search</h1>
<div class="flex flex-col gap-2">
    <form action="" id='search-form' method="GET" class="animation-form" data-swup-form>
        <label for="user-search" class="<?= labelStyles() ?>">Search user</label>
        <div class="flex gap-2">
            <input class="<?= inputStyles() ?>" name="user-search" id='user-search' placeholder="Enter a Username/Email" value="<?= $search ?>" />
            <button type="submit" id='search-button' class="<?= buttonStyles('w-max') ?>">Search</button>
        </div>
    </form>
</div>

<!-- Display users -->
<h2 class="my-4 mt-8 text-2xl">Users</h2>
<hr>

<div class="flex flex-col items-center" id='users-list'>
    <?php
    // Get paginated users
    $paginatedUsers = array_slice($userModel->getAllUsers(), $startIndex, $usersPerPage);

    foreach ($paginatedUsers as $user) {
        // Check if the user matches the search term
        if ($search === '' || strpos(strtolower($user['username']), $search) !== false || strpos(strtolower($user['email']), $search) !== false) {
            ?>
            <div class="flex items-center justify-between w-full gap-2 px-3 py-6" data-search='<?= $user['username'] . ',' . $user['email'] ?>'>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 overflow-hidden border rounded-md">
                        <?php Image($user['profile_image'], 1000, 1000, ['class'=>'object-contain max-h-10 max-w-10 w-full h-full !object-top']) ?>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="font-bold"><?= $user['username'] ?></h3>
                        <p class="text-sm text-muted-foreground"><?= $user['email'] ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    // Display pagination buttons with the search term
    ?>
    <div class="flex gap-3 py-5">
        <?php
        if ($page > 1) {
        ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>" class="<?= buttonStyles('','outline','icon') ?>">
                <?php chevronsLeft() ?>
            </a>
            <?php
        } else {
            ?>
            <button disabled class="<?= buttonStyles('','outline','icon') ?>">
                <?php chevronsLeft() ?>
            </button>
            <?php
        }

        $minPage = max(1, $page - 2);
        $maxPage = min($totalPages, $minPage + 4);

        for ($pageNum = $minPage; $pageNum <= $maxPage; $pageNum++) {
        ?>
            <a href="?page=<?= $pageNum ?>&search=<?= $search ?>" class="<?= buttonStyles('', 'outline', 'icon') . ($pageNum == $page ? '!bg-primary !text-primary-foreground' : '') ?>"><?= $pageNum ?></a>
        <?php
        }

        if ($page < $totalPages) {
        ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>" class="<?= buttonStyles('', 'outline', 'icon') ?>">
                <?php chevronsRight() ?>
            </a>
            <?php
        } else {
            ?>
            <button disabled class="<?= buttonStyles('','outline','icon') ?>">
                <?php chevronsRight() ?>
            </button>
            <?php
        }
        ?>
    </div>
</div>
