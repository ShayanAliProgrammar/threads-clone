<?php
if (!isset($_SESSION['user'])) {
    header("Location: /auth/sign-in");
    die();
}

$threadsModel = new Threads($db);

$threadId = $_GET['id'];

// Fetch the thread details
$threadDetails = $threadsModel->getThread($threadId);

// Check if the thread exists
if (!$threadDetails) {
    header('Location: /');
    die();
}

$title = "Thread By {$threadDetails['username']} - Meta Threads";
$description = $threadDetails['content'];
$keywords = str_replace(' ', ', ', $threadDetails['content']) . ", {$threadDetails['username']}";

$seo_image = $appUrl . $threadDetails['profile_image'];

// Assuming the page number is passed in the URL
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$commentsPerPage = 5; // Adjust the number of comments per page as needed
$offset = ($page - 1) * $commentsPerPage;

// Fetch comments for the thread with pagination
$comments = (new Comments($db))->getCommentsForThread($threadId, $commentsPerPage, $offset);

$totalCommentsNum = (new Comments($db))->getTotalCommentsForThread($threadId);

function displayComments($comments, $parentId = null)
{
    if (count($comments) == 0) {
        ?>
        <p class="my-2 text-sm text-muted-foreground">
            No comments yet.
        </p>
        <?php
    }
    foreach ($comments as $comment) {
        ?>
        <div class="<?= cardStyle('mt-2 ml-4 bg-muted') ?>">
            <div class="<?= cardContentStyle('') ?>">
                <div class="flex flex-wrap gap-2 sm:flex-nowrap">
                    <div class="relative overflow-hidden w-14">
                        <div class="w-10 h-10 border rounded-md bg-background">
                            <?php Image($comment['profile_image'], 1000, 1000, ['class' => 'object-contain max-h-10 max-w-10 w-full h-full !object-top']) ?>
                        </div>
                    </div>
                    <div class="flex flex-col w-full pt-2">
                        <h4 class="text-sm font-bold"><?= $comment['username'] ?></h4>
                        <p class="text-sm"><?= $comment['content'] ?></p>
                        <span class="mt-2 text-xs">
                            <?= formatDate($comment['date']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<div style="view-transition-name: thread-<?= $threadId ?>;" class="<?= cardStyle('!py-0 bg-muted') ?>">
    <div class="<?= cardContentStyle('') ?>">
        <!-- Thread Details -->
        <div class="flex flex-wrap gap-2 sm:flex-nowrap">
            <div class="relative overflow-hidden w-14">
                <div class="w-10 h-10 border rounded-md bg-background">
                    <?php Image($threadDetails['profile_image'], 1000, 1000, ['class' => 'object-contain max-h-10 max-w-10 w-full h-full !object-top']) ?>
                </div>
            </div>
            <div class="flex flex-col w-full pt-2">
                <h3 class="text-base font-bold"><?= $threadDetails['username'] ?></h3>
                <p class="text-sm mt-0.5"><?= $threadDetails['content'] ?></p>
                <div class="flex gap-2 mt-2">
                    <a href="/api/thread/toggle-like?id=<?= $threadId ?>" class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                        <?= heartIcon() ?>
                        <span class="sr-only">Like</span>
                        <span class="text-xs"><?= prettifyNumber($threadDetails['likes_count']) ?></span>
                    </a>
                    <button on:click='/js/display-comment-form.js' id='toggle-comment' class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                        <?= commentIcon() ?>
                        <span class="sr-only">Comment</span>
                        <span class="text-xs"><?= prettifyNumber($totalCommentsNum) ?></span>
                    </button>
                    <a target="_blank" data-no-swup href="https://wa.me/?text=<?= $currentUrl ?>" class="<?= buttonStyles('flex-col', 'ghost', 'icon') ?>">
                    <?= shareIcon() ?>
                        <span class="text-xs">Share</span>
                    </a>
                </div>

                <!-- Comment Form -->
                <div id="comment-form" class="<?= isset($_GET['comment']) ? '' : '!hidden' ?>">
                    <form action="/api/comment/add" method="POST" class="flex flex-col gap-2 my-5 ml-4">
                        <textarea name="comment" placeholder="Add a comment" class="<?= textareaStyles('resize-none') ?>" <?= isset($_GET['comment']) ? "autofocus" : "" ?>></textarea>

                        <input type="hidden" name="thread_id" value="<?= $_GET['id'] ?>">

                        <button type="submit" class="<?= buttonStyles('w-max') ?>">Comment</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div class="mx-auto mt-4 ml-4">
    <h3 class="text-xl font-bold">Comments</h3>
    <!-- Display paginated comments -->
    <?= displayComments($comments); ?>

    <!-- Pagination Links -->
    <div class="flex items-center justify-center">
        <div class="flex gap-3 py-5">
            <?php
            $totalPages = ceil($totalCommentsNum / $commentsPerPage);

            if ($page > 1) {
            ?>
                <a href="?id=<?= $threadId ?>&page=<?= $page - 1 ?>" class="<?= buttonStyles('', 'outline', 'icon') ?>">
                    <?php chevronsLeft() ?>
                </a>
            <?php
            } else {
            ?>
                <button disabled class="<?= buttonStyles('', 'outline', 'icon') ?>">
                    <?php chevronsLeft() ?>
                </button>
            <?php
            }

            $minPage = max(1, $page - 2);
            $maxPage = min($totalPages, $minPage + 4);

            for ($pageNum = $minPage; $pageNum <= $maxPage; $pageNum++) {
            ?>
                <a href="?id=<?= $threadId ?>&page=<?= $pageNum ?>" class="<?= buttonStyles('', 'outline', 'icon') . ($pageNum == $page ? '!bg-primary !text-primary-foreground' : '') ?>"><?= $pageNum ?></a>
            <?php
            }

            if ($page < $totalPages) {
            ?>
                <a href="?id=<?= $threadId ?>&page=<?= $page + 1 ?>" class="<?= buttonStyles('', 'outline', 'icon') ?>" id="next">
                    <?php chevronsRight() ?>
                </a>
            <?php
            } else {
            ?>
                <button disabled class="<?= buttonStyles('', 'outline', 'icon') ?>">
                    <?php chevronsRight() ?>
                </button>
            <?php
            }
            ?>
        </div>
    </div>
</div>
