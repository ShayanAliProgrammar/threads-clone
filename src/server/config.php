<?php

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'threads-clone');

// Start session
if(!session_id()){
    session_start();
}