<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'auctionsystem');
define('DB_USER', 'root');
define('DB_PASSWORD', 't372Yf*e');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>