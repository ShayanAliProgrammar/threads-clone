<?php

// Remove token and user data from the session
unset($_SESSION['user']);

session_destroy();

// Redirect to homepage
header("Location: /");
die();