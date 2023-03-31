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

if (isset($_SESSION['success'])) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])) : ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif;

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

$now = new DateTime();
$end_time = new DateTime($auction['end_time']);
$interval = $now->diff($end_time);
$time_left = $interval->format('%d days %H:%I:%S');
// var_dump($time_left);


// Check if the bid form has been submitted
if (isset($_POST['action'])) {
    // Get the current highest bid for the auction
    $sql = "SELECT MAX(amount) AS highest_bid FROM bids WHERE auction_id = $auction_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $highest_bid = $row['highest_bid'];

    // Get the user ID from the session (you need to implement this)
    $user_id = $_SESSION['id'];

    // Get the bid amount from the form
    $bid_amount = $_POST['bidAmount'];

    // Determine the new bid amount based on the form submission
    if ($_POST['action'] == 'bidAmount') {
        $new_bid_amount = $bid_amount;
    } else {
        $new_bid_amount = $auction['price'] * 1.1;
    }

    // Check if the new bid amount is higher than the current highest bid
    if ($new_bid_amount > $highest_bid) {
        // Insert the new bid into the database
        $sql = "INSERT INTO bids (amount, user_id, auction_id) VALUES ($new_bid_amount, $user_id, $auction_id)";
        mysqli_query($conn, $sql);

        // Update the auction price in the database
        $sql = "UPDATE auctions SET price = $new_bid_amount WHERE id = $auction_id";
        mysqli_query($conn, $sql);

        // Update the $auction variable with the new auction price
        $auction['price'] = $new_bid_amount;

        // Show a success message
        $message = "Bid successfully submitted!";
        $messageClass = "text-success mt-4";
        $sql = "SELECT * FROM users WHERE id = $user_id";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $sql = "SELECT * FROM auctions WHERE id = $auction_id";
        $result = $conn->query($sql);
        $auction = $result->fetch_assoc();


        // insert notification into database
        $sql = "INSERT INTO notifications (user_id, user_name, auction_id, auction_name, bid_amount, timestamp,auction_user_id, bid_user_id)
        VALUES ($user_id, '{$user['email']}', $auction_id, '{$auction['name']}', $new_bid_amount, NOW(),{$auction['user_id']} ,$user_id)";
        $conn->query($sql);
    } else {
        // Show an error message
        $message = "Your bid must be higher than the current highest bid!";
        $messageClass = "text-danger mt-4";
    }
}


if (isset($_POST['action10%'])) {
    // Get the current highest bid for the auction
    $sql = "SELECT MAX(amount) AS highest_bid FROM bids WHERE auction_id = $auction_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $highest_bid = $row['highest_bid'];

    // Get the user ID from the session (you need to implement this)
    $user_id = $_SESSION['id'];

    // Determine the new bid amount based on the form submission
    $new_bid_amount = $auction['price'] * 1.1;


    // Check if the new bid amount is higher than the current highest bid
    if ($new_bid_amount > $highest_bid) {
        // Insert the new bid into the database
        $sql = "INSERT INTO bids (amount, user_id, auction_id) VALUES ($new_bid_amount, $user_id, $auction_id)";
        mysqli_query($conn, $sql);

        // Update the auction price in the database
        $sql = "UPDATE auctions SET price = $new_bid_amount WHERE id = $auction_id";
        mysqli_query($conn, $sql);

        // Update the $auction variable with the new auction price
        $auction['price'] = $new_bid_amount;

        // Show a success message
        $message = "Bid successfully submitted!";
        $messageClass = "text-success mt-4";




        $sql = "SELECT * FROM users WHERE id = $user_id";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $sql = "SELECT * FROM auctions WHERE id = $auction_id";
        $result = $conn->query($sql);
        $auction = $result->fetch_assoc();
        // var_dump($auction);
        // var_dump($user);


        // insert notification into database
        $sql = "INSERT INTO notifications (user_id, user_name, auction_id, auction_name, bid_amount, timestamp,auction_user_id, bid_user_id)
        VALUES ($user_id, '{$user['email']}', $auction_id, '{$auction['name']}', $new_bid_amount, NOW(),{$auction['user_id']} ,$user_id)";
        $conn->query($sql);
    } else {
        // Show an error message
        $message = "Your bid must be higher than the current highest bid!";
        $messageClass = "text-danger mt-4";
    }
}


// Get current bids
$sql = "SELECT u.username, b.amount, b.created_at FROM bids b JOIN users u ON b.user_id=u.id WHERE b.auction_id=:auction_id ORDER BY b.amount DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['auction_id' => $auction_id]);
$current_bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Auction Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <style>
        tbody tr:first-child {
            background-color: #00c92ead;

        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <img src="uploads/<?php echo $auction['photo'] ?>" alt="Auction Image" class="img-fluid">
            </div>
            <div class="col-md-6 border rounded border-2 p-3">
                <h1 class="mb-4"><?php echo $auction['name']; ?></h1>
                <p class="mb-4"><?php echo $auction['description'] ?></p>
                <div class="d-flex justify-content-between">
                    <div>
                        <div id="countdown"></div>
                        <h4 class="mb-4 "><?php echo '<span class="countdown" data-end-time="' . $auction['end_time'] . '"></span>' ?></h4>
                        <h3 class="mb-4"><?php echo $auction['price'] ?>$</h3>
                    </div>
                    <div>
                        <?php if ($interval->invert === 0) { ?>
                            <?php if ($_SESSION['id'] != $auction['user_id']) { ?>
                                <div class="">
                                    <form method="post">
                                        <div class="mb-3">
                                            <label for="bidAmount" class="form-label">Bid amount:</label>
                                            <input type="number" id="bidAmount" name="bidAmount" class="form-control">
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="action" value="bidAmount">Bid from amount</button>

                                        <button class="btn btn-primary" type="submit" name="action10%" value="bidUp">Bid 10%</button>
                                </div>

                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>



                <?php if (isset($message)) { ?>
                    <p class="<?php echo $messageClass ?>"><?php echo $message; ?></p>

                <?php } ?>
                <?php if ($_SESSION['id'] === $auction['user_id']) : ?>
                    <div class="d-flex justify-content-center align-content-center p-4 bg-danger bg-opacity-25 rounded">
                        <h4 class="text-danger">You can not bid on your own auction !!!</h4>

                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-4">
            <h3>Current Bids:</h3>
            <?php if ($current_bids) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bid Amount</th>
                            <th>Bidder Name</th>
                            <th>Bid Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($current_bids as $bid) : ?>
                            <tr>
                                <td>$<?= $bid['amount'] ?></td>
                                <td><?= $bid['username'] ?></td>
                                <td><?= date('m/d/Y h:i A', strtotime($bid['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="d-flex justify-content-center align-content-center p-3 bg-secondary bg-opacity-25 rounded mt-4">
                    <h6 class="text-secondary">NO BODY BID THIS AUCTION</h6>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // update countdown every second
        console.log(document.getElementsByClassName('countdown'));
        setInterval(function() {
            let now = new Date().getTime();
            let countdowns = document.getElementsByClassName('countdown');
            for (let i = 0; i < countdowns.length; i++) {
                let endTime = new Date(countdowns[i].getAttribute('data-end-time'));
                let secondsLeft = Math.floor((endTime - now) / 1000) + 3600;
                if (secondsLeft < 0) {
                    countdowns[i].innerHTML = 'FINISHED';
                    continue;
                }
                let days = Math.floor(secondsLeft / (3600 * 24));
                secondsLeft -= days * 3600 * 24;
                let hours = Math.floor(secondsLeft / 3600);
                secondsLeft -= hours * 3600;
                let minutes = Math.floor(secondsLeft / 60);
                secondsLeft -= minutes * 60;
                let seconds = secondsLeft;
                countdowns[i].innerHTML = days + ' days ' + formatNumber(hours) + ':' + formatNumber(minutes) + ':' + formatNumber(seconds);

            }
        },200);

        function formatNumber(num) {
            return (num < 10 ? '0' : '') + num;
        }
    </script>
</body>

</html>