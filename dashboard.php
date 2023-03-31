<?php
require_once 'config.php';
require_once 'db.php';
session_start();
include 'navbar.php';


// Fetch all auctions
$sql = "SELECT *,
  CASE WHEN end_time <= NOW() THEN 1 ELSE 0 END AS is_expired
FROM auctions
ORDER BY is_expired ASC, end_time ASC";
$stmt = $pdo->query($sql);
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Auction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container py-5">
        <h1 class="mb-4">Auctions</h1>
        <?php if (isset($_SESSION['success'])) : ?>
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
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table align-middle mb-0 bg-white">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Time Left</th>
                        <th>Price</th>
                        <th>Action</th>
                        <!-- <th>Status</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auctions as $auction) : ?>
                        <?php
                        $now = new DateTime();
                        $end_time = new DateTime($auction['end_time']);
                        $interval = $now->diff($end_time);
                        $time_left = $interval->format('%d days %H:%I:%S');
                        // Check if auction is active
                        if ($interval->invert === 1) {
                            // $status = 'Inactive';
                            $bg_color = 'table-secondary';
                            $time_left = 'FINISHED';
                            $auctionHref = 'auction_detail.php?id=' . $auction['id'];
                            $auctionBtn = 'btn btn-secondary';
                            $auctionBtnText = 'Expired';
                        } else {
                            // $status = 'Active';
                            $bg_color = '';
                            $auctionHref = 'auction_detail.php?id=' . $auction['id'];
                            $auctionBtn = 'btn btn-primary';
                            $auctionBtnText = 'Bid Now';
                            // add live countdown
                            $time_left = '<span class="countdown" data-end-time="' . $auction['end_time'] . '"></span>';
                        }
                        ?>
                        <tr class="<?php echo $bg_color; ?>">
                            <td><img src="uploads/<?php echo $auction['photo']; ?>" alt="<?php echo $auction['name']; ?>" style="max-height: 100px;"></td>
                            <td style="width: 15%;"><?php echo $auction['name']; ?></td>
                            <td style="width: 15%; max-width: 50px;" class="text-truncate"><?php echo $auction['description']; ?></td>
                            <td style="width: 15%;"><?php echo $time_left; ?></td>
                            <td style="width: 15%;"><?php echo number_format($auction['price'], 2); ?></td>
                            <td style="width: 15%;">
                                <a href="<?php echo $auctionHref; ?>" class="<?php echo $auctionBtn; ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>



            </table>
        </div>
    </div>
    <script>
        // update countdown every second
        
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