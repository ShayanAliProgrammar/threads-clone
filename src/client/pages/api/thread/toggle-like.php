<?php

// Check if the thread ID is provided in the URL
if (isset($_GET['id'])) {
    $threadId = $_GET['id'];

    $likesModel = new Likes($db);

    // Get the user ID from your authentication system
    $userId = $_SESSION['user']['id']; // Replace with the actual user ID

    // Toggle the like for the thread
    $result = $likesModel->toggleLikeForThread($userId, $threadId);

    if ($result) {
        header('Location: /thread?id=' . $threadId);
    } else {
        // Error occurred
        header('Location: /?errors=Something_went_wrong.');
    }

} else {
    header('Location: /?errors=Something_went_wrong.');
}
?>