<?php
require_once 'config.php';
require_once 'db.php';
session_start();
include 'navbar.php';

// Get auction ID from URL parameter
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Invalid auction ID';
    header('Location: dashboard.php');
    exit();
}
$auction_id = $_GET['id'];

// Get auction data
$sql = "SELECT * FROM auctions WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $auction_id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if auction exists
if (!$auction) {
    $_SESSION['error'] = 'Auction not found';
    header('Location: dashboard.php');
    exit();
}

// Check if user is the creator of the auction
if ($_SESSION['id'] == $auction['id']) {
    $_SESSION['error'] = 'You cannot bid on your own auction';
    header('Location: dashboard.php');
    exit();
}

// Handle bidding form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get bid amount from form
    $bid_amount = $_POST['bid_amount'];
    // Calculate new price
    $new_price = $auction['price'] + $bid_amount;
    // Add new bid to database
    $sql = "INSERT INTO bids (user_id, auction_id, bid_amount, new_price) VALUES (:user_id, :auction_id, :bid_amount, :new_price)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'auction_id' => $auction_id, 'bid_amount' => $bid_amount, 'new_price' => $new_price]);
    // Update auction price
    $sql = "UPDATE auctions SET price=:new_price WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['new_price' => $new_price, 'id' => $auction_id]);
    // Redirect to auction detail page
    $_SESSION['success'] = 'Bid placed successfully';
    header("Location: auction_detail.php?id=$auction_id");
    exit();
}

// Get all bids for the auction
$sql = "SELECT b.*, u.username FROM bids b INNER JOIN users u ON b.user_id=u.id WHERE auction_id=:auction_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['auction_id' => $auction_id]);
$bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $auction['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Auction</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle
 -->


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Auction Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <img src="" alt="Auction Image" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h1 class="mb-4">Auction Name</h1>
                <h3 class="mb-4">Auction Price: $XXX</h3>
                <h4 class="mb-4">Time Left: XX days XX hours XX minutes XX seconds</h4>
                <p class="mb-4">Auction Description</p>
                <form>
                    <div class="mb-3">
                        <label for="bid-amount" class="form-label">Bid Amount</label>
                        <input type="number" class="form-control" id="bid-amount" name="bid-amount" placeholder="Enter Bid Amount" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Bid Now</button>
                </form>
            </div>
        </div>
        <hr>
        <h2 class="mb-4">Current Bids</h2>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Bid Amount</th>
                    <th>Bid Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>User 1</td>
                    <td>$XXX</td>
                    <td>XX days XX hours ago</td>
                </tr>
                <tr>
                    <td>User 2</td>
                    <td>$XXX</td>
                    <td>XX days XX hours ago</td>
                </tr>
                <tr>
                    <td>User 3</td>
                    <td>$XXX</td>
                    <td>XX days XX hours ago</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>