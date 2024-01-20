<?php

if (isset($_SESSION['user'])) {
    header('Location: /');
    die();
}


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted email/username and password
    $emailOrUsername = $_POST['email_or_username'];
    $password = $_POST['password'];

    // Create an instance of the User model
    $userModel = new User($db); // Assuming $db is already available

    // Attempt to authenticate the user
    $user = $userModel->authenticateUser($emailOrUsername, $password);


    if ($user !== null) {
        $user['profile_image'] = (new Model())->decrypt($user['profile_image']);
        $_SESSION['user'] = unserialize(serialize($user));
        // Authentication successful, redirect to a dashboard or home page
        header('Location: /'); // Adjust the redirect URL
        exit();
    }
    // Authentication failed, display an error message
    header("Location: /auth/sign-in?errors=invalid_credentials");
    exit();

} else {
    echo "Method Not Allowed";
}