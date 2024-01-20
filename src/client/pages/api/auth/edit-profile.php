<?php

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /auth/sign-in');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userModel = new Model();
    $sqlHelper = new SQLHelper($db);

    // Validate and sanitize user input (add your validation logic here)
    $username = $_POST['username'];
    $email = $_POST['email'];

    $dataToUpdate = array(
        'username' => $userModel->encrypt($username),
        'email' => $userModel->encrypt($email),
    );

    // Handle image upload
    if (isset($_FILES['profile_image']) && ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK)) {
        $uploadDir = '/images/';
        $uploadFile = $uploadDir . uniqid() . "-" . basename($_FILES['profile_image']['name']);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], __DIR__ . '/../../../public' . $uploadFile)) {
            $dataToUpdate['profile_image'] = $userModel->encrypt($uploadFile);
        } else {
            header("Location: /auth/profile?errors=error_uploading_image.");
            exit;
        }
    }

    // Update the user data in the database
    $userId = $_SESSION['user']['id'];
    $where = 'id = ?';
    $params = array($userId);

    $result = $sqlHelper->executeUpdate('users', $dataToUpdate, $where, $params);

    if ($result) {
        // Update the user data in the session
        $_SESSION['user']['username'] = $userModel->encrypt($username);
        $_SESSION['user']['email'] = $userModel->encrypt($email);
        if (isset($dataToUpdate['profile_image'])) {
            $_SESSION['user']['profile_image'] = $dataToUpdate['profile_image'];
        }

        // Redirect to profile page on success
        header('Location: /auth/profile');
        exit;
    } else {
        header("Location: /auth/profile?errors=error_updating_user_profile.");
        exit;
    }
} else {
    echo "Method Not Allowed";
}
?>
