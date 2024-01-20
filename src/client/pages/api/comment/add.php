<?php

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: /auth/sign-in");
    die();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadsModel = new Threads($db);

    // Assuming the thread ID is passed in the form data
    $threadId = $_POST['thread_id'];

    // Fetch the thread details
    $threadDetails = $threadsModel->getThread($threadId);

    // Check if the thread exists
    if (!$threadDetails) {
        header('Location: /');
        die();
    }

    $commentContent = isset($_POST['comment']) ? $_POST['comment'] : '';

    // Add the comment
    $result = (new Comments($db))->addComment($_SESSION['user']['id'], $threadId, $commentContent);

    if ($result) {
        // Redirect to the thread page after adding the comment
        header("Location: /thread?id=$threadId");
        die();
    } else {
        // Handle error
        header('Location: /?error=error_adding_comment');
        die();
    }
} else {
    // Handle invalid request method
    echo "Method not Allowed";
}
