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

// Check if the auction ID is provided
if (!isset($_GET['id'])) {
  header("location: my_auctions.php");
  exit;
}

// Retrieve the auction from the database
$stmt = $pdo->prepare('SELECT * FROM auctions WHERE id = ? AND user_id = ?');
$stmt->execute([$_GET['id'], $_SESSION['id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the auction exists and belongs to the user, redirect to my auctions page if not
if (!$auction) {
  header("location: my_auctions.php");
  exit;
}

// Update the auction if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate form data
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $price = floatval($_POST['price']);
  $endtime = $_POST['endtime'];

  $errors = [];
  if (empty($name)) {
    $errors['name'] = 'Please enter an item name.';
  }
  if (empty($description)) {
    $errors['description'] = 'Please enter a description.';
  }
  if ($price <= 0) {
    $errors['price'] = 'Please enter a valid price.';
  }
  if (empty($endtime)) {
    $errors['endtime'] = 'Please enter an end time.';
  } elseif (strtotime($endtime) === false) {
    $errors['endtime'] = 'Please enter a valid end time.';
  }

  // Update the auction if there are no errors
  if (count($errors) === 0) {
    $stmt = $pdo->prepare('UPDATE auctions SET name = ?, description = ?, price = ?, end_time = ? WHERE id = ?');
    
    if ($stmt->execute([$name, $description, $price, $endtime, $auction['id']])) {
      $_SESSION['success'] = 'Auction edited successfully';
      header('Location: dashboard.php');
      exit;
    } else {
      $_SESSION['error'] = 'Something went wrong';
      header('Location: dashboard.php');
      exit;
    }
    header("location: dashboard.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta https-equiv="X-UA-Compatible" content="ie=edge">
  <title>Edit Auction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js
  "></script>

</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h1>Edit Auction</h1>
    <form method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Item Name</label>
        <input type="text" class="form-control <?php if (isset($errors['name'])) echo 'is-invalid'; ?>" id="name" name="name" value="<?php echo htmlspecialchars($auction['name']); ?>">
        <?php if (isset($errors['name'])) echo '<div class="invalid-feedback">' . htmlspecialchars($errors['name']) . '</div>'; ?>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control <?php if (isset($errors['description'])) echo 'is-invalid'; ?>" id="description" name="description" rows="3"><?php echo htmlspecialchars($auction['description']); ?></textarea>
        <?php if (isset($errors['description'])) echo '<div class="invalid-feedback">' . htmlspecialchars($errors['description']) . '</div>'; ?>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="text" class="form-control <?php if (isset($errors['price'])) echo 'is-invalid'; ?>" id="price" name="price" value="<?php echo htmlspecialchars($auction['price']); ?>">
        </div>
        <?php if (isset($errors['price'])) echo '<div class="invalid-feedback">' . htmlspecialchars($errors['price']) . '</div>'; ?>
      </div>
      <div class="mb-3">
        <label for="endtime" class="form-label">End Time</label>
        <input type="text" class="form-control <?php if (isset($errors['end_time'])) echo 'is-invalid'; ?>" id="endtime" name="endtime" value="<?php echo htmlspecialchars($auction['end_time']); ?>">
        <?php if (isset($errors['end_time'])) echo '<div class="invalid-feedback">' . htmlspecialchars($errors['end_time']) . '</div>'; ?>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</body>

</html>