<?php
if (!isset($_SESSION['user'])) {
    header('Location: /auth/sign-in');
    die();
}

// Define the number of threads per page
$threadsPerPage = 5;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;


$offset = ($page - 1) * $threadsPerPage;

// Fetch threads for the current page
$threadsModel = new Threads($db);

if (!isset($_GET['search'])){
    $allThreads = $threadsModel->getPaginatedThreads($threadsPerPage, $offset);

    // Calculate the total number of pages
    $totalPages = ceil($threadsModel->getTotalThreadCount() / $threadsPerPage);
} else {
    $allThreads = $threadsModel->searchThreads($threadsPerPage, $offset, $_GET['search']);

    $totalPages = ceil($threadsModel->getTotalThreadCountBySearch($threadsPerPage, $offset, $_GET['search']) / $threadsPerPage);
}
?>

<h1 class="mb-4 text-3xl font-bold">Home</h1>

<form action="/api/threads/create" method="POST" class="flex flex-col gap-2">

    <?php
    if (isset($_GET['errors'])){
    ?>
        <ul class="p-2 mb-4 text-sm rounded bg-destructive text-destructive-foreground">
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

    <label for="thread" class="<?= labelStyles() ?>">Content</label>
    <textarea class="<?= textareaStyles('min-h-[90px] resize-none max-h-[90px]') ?>" name="thread_content" id='thread' placeholder="What's in your mind?"></textarea>
    <button class="<?= buttonStyles('w-max') ?>">Create</button>
</form>

<div class="flex flex-col gap-2 mb-4">
    <h2 class="my-2 mt-8 text-2xl">Threads</h2>

    <form action="#" data-swup-form>
        <label for="search" class="<?= labelStyles('') ?>">Search</label>
        <div class="flex items-center gap-2">
            <input type="text" class="<?= inputStyles() ?>" name="search" id="search" placeholder="Username, Email or Thread content" />
            <button type="submit" class="<?= buttonStyles() ?>">Search</button>
        </div>
    </form>
</div>
<hr>

<div class="flex flex-col items-center justify-center w-full gap-5 px-3 my-5">
    <?php
    foreach ($allThreads as $thread) {
    ?>
    <div style="view-transition-name: thread-<?= $thread['id'] ?>;" class="<?= cardStyle('!py-0 bg-muted w-full') ?>">
        <div class="<?= cardContentStyle('') ?>">
            <div class="flex flex-wrap gap-2 sm:flex-nowrap">
                <div class="relative flex justify-center overflow-hidden w-14">
                    <div class="w-10 h-10 border rounded-md bg-background">
                        <?php Image($thread['profile_image'], 1000, 1000, ['class'=>'object-contain max-h-10 max-w-10 w-full h-full !object-top']) ?>
                    </div>
                </div>
                <div class="flex flex-col w-full pt-2">
                    <h3 class="text-base font-bold"><?= $thread['username'] ?></h3>
                    <a href="/thread?id=<?= $thread['id'] ?>" class="text-sm mt-0.5 focus:!outline-none hover:underline focus:underline  line-clamp-2"><?= $thread['content'] ?></a>
                    <div class="flex gap-2 mt-2">
                        <a href="/api/thread/toggle-like?id=<?= $thread['id'] ?>" class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                            <?= heartIcon() ?>
                            <span class="sr-only">Like</span>
                            <span class="text-sm"><?= prettifyNumber($thread['likes_count']) ?></span>
                        </a>
                        <a href="/thread?id=<?= $thread['id'] ?>&comment=1" class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                            <?= commentIcon() ?>
                            <span class="sr-only">Comment</span>
                            <span class="text-sm"><?= prettifyNumber($thread['comments']) ?></span>
                        </a>
                        <a target="_blank" data-no-swup href="https://wa.me/?text=<?= $currentUrl . 'thread?id=' . $thread['id'] ?>" class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                        <?= shareIcon() ?>
                            <span class="text-xs">Share</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>

    <!-- Pagination Links -->
    <div class="flex gap-3 py-5">
        <?php
        if ($page > 1) {
        ?>
            <a href="?page=<?= $page - 1 ?>" class="<?= buttonStyles('','outline','icon') ?>">
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
            <a href="?page=<?= $pageNum ?>" class="<?= buttonStyles('', 'outline', 'icon') . ($pageNum == $page ? '!bg-primary !text-primary-foreground' : '') ?>"><?= $pageNum ?></a>
        <?php
        }

        if ($page <= $totalPages) {
        ?>
            <a href="?page=<?= $page + 1 ?>" class="<?= buttonStyles('', 'outline', 'icon') ?>" id="next">
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
