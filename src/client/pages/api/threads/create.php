<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadsModel = new Threads($db);

    $userId = $_SESSION['user']['id'];

    $content = isset($_POST['thread_content']) ? $_POST['thread_content'] : '';

    // Validate and sanitize the input as needed

    // Create the thread
    $result = $threadsModel->createThread($userId, $content);

    if ($result !== 0) {
        header('Location: /thread?id=' . $result);
        exit(); // Ensure that no further code is executed after the redirect
    } else {
        // Error creating thread
        header('Location: /?error=error_creating_thread');
        exit(); // Ensure that no further code is executed after the redirect
    }
} else {
    // Handle non-POST requests as needed
    echo "Method not Allowed";
}
