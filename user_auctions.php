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

// Retrieve all auctions belonging to the user
$stmt = $pdo->prepare('SELECT * FROM auctions WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['id']]);
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($auctions)
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta https-equiv="X-UA-Compatible" content="ie=edge">
  <title>My Auctions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
</head>

<body>
  <?php include_once 'navbar.php'; ?>

  <div class="container mt-3">
    <h2>My Auctions</h2>
    <?php if (count($auctions) > 0) : ?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Item Name</th>
            <th>Description</th>
            <th>Current Price</th>
            <th>Created At</th>
            <th>End Time</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($auctions as $auction) : ?>
            <tr>
              <td class="align-middle"><img src="uploads/<?php echo $auction['photo']; ?>" alt="<?php echo $auction['name']; ?>" style="max-height: 100px;"></td>
              <td class="align-middle"><?= $auction['name'] ?></td>
              <td class="align-middle text-truncate" style="max-width: 100px;"><?= $auction['description'] ?></td>
              <td class="align-middle"><?= $auction['price'] ?></td>
              <td class="align-middle"><?= $auction['created_at'] ?></td>
              <td class="align-middle"><?= $auction['end_time'] ?></td>
              <td class="align-middle">
                <?php if ($auction['end_time'] <= date('Y-m-d H:i:s')) :
                ?>
                  <a href="auction_detail.php?id=<?= $auction['id'] ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                  <a href="delete_auction.php?id=<?= $auction['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this auction?')"><i class="fa-solid fa-trash"></i></a>

                <?php else : ?>
                  <a href="auction_detail.php?id=<?= $auction['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                  
                  <a href="edit_auction.php?id=<?= $auction['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                  <a href="delete_auction.php?id=<?= $auction['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this auction?')"><i class="fa-solid fa-trash"></i></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else : ?>
      <p>You have not created any auctions yet.</p>
      <a href="create_auction.php" class="btn btn-success mt-3">Create Auction</a>
    <?php endif; ?>




  </div>
</body>

</html>