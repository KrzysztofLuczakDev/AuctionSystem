<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Auction</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    
<?php
  session_start();
  include 'navbar.php';
?>

  <div class="container mt-5">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header">
            <h3 class="text-center">Create Auction</h3>
          </div>
          <div class="card-body">
            <form action="save_auction.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo" class="form-control-file" required>
              </div>
              <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="time">Time (in hours)</label>
                <input type="number" name="time" id="time" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Create Auction</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popperjs.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>