<?php
// Include the config file
require_once 'config.php';
require_once 'db.php';

// Initialize the session
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['email']) || !$_SESSION['loggedin']) {
  header("location: login.php");
  exit;
}

// Check if the auction ID was provided in the URL
if (!isset($_GET['id'])) {
  header("location: my_auctions.php");
  exit;
}

// Retrieve the auction from the database
$stmt = $pdo->prepare('SELECT * FROM auctions WHERE id = ?');
$stmt->execute([$_GET['id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the auction belongs to the user
if (!$auction || $auction['user_id'] !== $_SESSION['id']) {
  header("location: my_auctions.php");
  exit;
}

// Delete the auction and its bids from the database
$pdo->beginTransaction();
$pdo->prepare('DELETE FROM bids WHERE auction_id = ?')->execute([$auction['id']]);
$pdo->prepare('DELETE FROM auctions WHERE id = ?')->execute([$auction['id']]);
$pdo->commit();

if ($stmt->execute()) {
  $_SESSION['success'] = 'Auction deleted successfully';
  header('Location: dashboard.php');
  exit;
} else {
  $_SESSION['error'] = 'Something went wrong';
  header('Location: dashboard.php');
  exit;
}

?>
