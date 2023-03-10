<?php
require_once 'config.php';
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    $user_id = $_SESSION["id"];

    $photo_name = $_FILES['photo']['name'];
    $photo_tmp_name = $_FILES['photo']['tmp_name'];
    $photo_size = $_FILES['photo']['size'];
    $photo_type = $_FILES['photo']['type'];
    $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($photo_ext, $allowed_ext)) {
        $_SESSION['error'] = 'Only JPG, JPEG, PNG, and GIF files are allowed';
        header('Location: create_auction.php');
        exit;
    }

    if ($photo_size > 5000000) {
        $_SESSION['error'] = 'File size must be less than 5 MB';
        header('Location: create_auction.php');
        exit;
    }

    $end_time = date('Y-m-d H:i:s', strtotime("+{$time} hours"));

    $sql = "INSERT INTO auctions (name, photo, price, end_time, description, user_id) 
            VALUES (:name, :photo, :price, :end_time, :description, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':photo', $photo_name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        move_uploaded_file($photo_tmp_name, "/Users/krzysztofluczak/Documents/DeveloperWorkspaces/AuctionSystem/AuctionSystem/uploads/$photo_name");
        $_SESSION['success'] = 'Auction created successfully';
        header('Location: dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = 'Something went wrong';
        header('Location: create_auction.php');
        exit;
    }
}