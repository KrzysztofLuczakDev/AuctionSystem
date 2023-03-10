<?php
session_start();
require_once 'config.php';
require_once 'navbar.php';

// retrieve notifications from database
if (isset($_SESSION['id'])) {
  $user_id = $_SESSION['id'];
  $sql = "SELECT * FROM notifications WHERE auction_user_id = $user_id ORDER BY timestamp DESC";
  $result = $conn->query($sql);

  // mark notifications as read if user has visited page
  $sql = "UPDATE notifications SET is_read = 1 WHERE auction_user_id = $user_id";
  $conn->query($sql);
} else {
  header('Location: login.php');
  exit();
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container">
      <h1 class="mt-4 mb-4">Notifications</h1>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="card mb-3 <?php echo $row['is_read'] ? '' : 'bg-success bg-opacity-25'; ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo $row['user_name']; ?> bid on "<?php echo $row['auction_name']; ?>"</h5>
              <p class="card-text">Bid amount: $<?php echo $row['bid_amount']; ?></p>
              <p class="card-text">Bid time: <?php echo $row['timestamp']; ?></p>
              <a href="auction_detail.php?id=<?php echo $row['auction_id']; ?>" class="btn btn-primary">Visit Auction</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No notifications to display.</p>
      <?php endif; ?>
    </div>
  </body>
</html>
