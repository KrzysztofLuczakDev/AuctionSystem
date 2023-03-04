<?php
// Replace the placeholders with your own database credentials
$host = 'localhost';
$dbname = 'systemaukcyjny';
$user = 'root';
$password = 't372Yf*e';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If there is an error, display the error message
    echo "Connection failed: " . $e->getMessage();
}