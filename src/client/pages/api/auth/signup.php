<?php

if (isset($_SESSION['user'])) {
    header('Location: /');
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($db);

    // Validate and sanitize user input (add your validation logic here)

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Process signup form submission
    $dbUser = $user->createUser($username, $email, $password);

    if ($dbUser == 2){
        header('Location: /auth/sign-up?errors=invalid_credentials');
        exit;
    } elseif ($dbUser == 0){
        header('Location: /auth/sign-up?errors=internal-error');
        exit;
    }


    // Redirect to sign-in page on success
    header('Location: /auth/sign-in');
    exit;
} else {
    echo "Method Not Allowed";
}
