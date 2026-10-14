<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'secure' => $isHttps, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}

if (empty($_SESSION['USER_NUM'])) {
    header("location: tracking.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <!-- Bootstrap CSS -->
    <link href="../css/cssreset.css" rel="stylesheet" />
    <link href="../css/bootstrap.css" rel="stylesheet" />
    <link href="../css/main.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/cbdf2b0f0e.js" crossorigin="anonymous"></script>
    <title>NEXTGEN - Tracking information</title>
  </head>
  <body>
<?php
  require('header.php');
?>
<?php
    $num = (string)$_SESSION['USER_NUM'];
    require_once __DIR__ . '/../database/connection.php';
    try {
        $stmt = app_db()->prepare('SELECT * FROM tracking WHERE tracking_number = :number LIMIT 1');
        $stmt->execute([':number' => $num]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $exception) {
        error_log('Tracking details lookup failed: ' . $exception->getMessage());
        http_response_code(503);
        exit('Tracking information is temporarily unavailable.');
    }
    if (!$row) {
        unset($_SESSION['USER_NUM']);
        $_SESSION['ERRMSG_ARR'] = ['We could not find that shipment. Please track it again.'];
        header('Location: tracking.php');
        exit();
    }
    $id = $row['id'];
    $num = $row['tracking_number'];
    $shipdate = $row['shipment_date'];
    $estimate_date = $row['delivery_date'];
    $ship_type = $row['shipment_type'];
    $content = $row['content'];
    $receiver = $row['first_name'] . ' ' . $row['last_name'];
    $source_address = $row['source_city'] . ', ' . $row['source_state'] . ', ' . $row['source_zip'];
    $current_address = $row['current_city'] . ', ' . $row['current_state'] . ', ' . $row['current_zip'];
    $destination_address = $row['destination_city'] . ', ' . $row['destination_state'] . ', ' . $row['destination_zip'];
    $phone = $row['contact_number'];
    $status = $row['parcel_status'];
    function tracking_escape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
?>

<section class="page-title-secttion">
    <div class="container">
      <div class="page-header">
        <h1>Tracked Parcel Information</h1>
      </div>
      <ol class="breadcrumb justify-content-center">
        <li><a href="#">Tracking Number </a></li>
        <li class="active">: <?php echo tracking_escape($num); ?></li>
      </ol>
    </div>
</section>
<div class="container mb-5">
    <div class="row">
      <div class="col-12 col-md-6">
        <h3 class="my-4">Parcel Information</h3>
        <div class="table-responsive border">          
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Parcel</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr class="boldder">
                <td>Tracking Number</td>
                <td><?php echo tracking_escape($num); ?></td>
              </tr>
              <tr class="boldder">
                <td>Ship Date</td>
                <td><?php echo tracking_escape($shipdate); ?></td>
              </tr>
              <tr class="boldder">
                <td>Estimated Delivery Date</td>
                <td><?php echo tracking_escape($estimate_date); ?></td>
              </tr>
              <tr class="boldder">
                <td>Shipment Type</td>
                <td><?php echo tracking_escape($ship_type); ?></td>
              </tr>
              <tr class="boldder">
                <td>Parcel Content</td>
                <td><?php echo tracking_escape($content); ?></td>
              </tr>
              <tr class="boldder">
                <td>Receiver Name</td>
                <td><?php echo tracking_escape($receiver); ?></td>
              </tr>
              <tr class="boldder">
                <td>Source Address</td>
                <td><?php echo tracking_escape($source_address); ?></td>
              </tr>
              <tr class="boldder">
                <td>Current Location</td>
                <td><?php echo tracking_escape($current_address); ?></td>
              </tr>
              <tr class="boldder">
                <td>Destination Address</td>
                <td><?php echo tracking_escape($destination_address); ?></td>
              </tr>
              <tr class="boldder">
                <td>Contact Number</td>
                <td><?php echo tracking_escape($phone); ?></td>
              </tr>
              <tr class="boldder">
                <td>Parcel Status</td>
                <td><?php echo tracking_escape($status); ?></td>
              </tr>
            </tbody>
          </table>
          </div>
      </div>
      <div class="col-12 col-md-6">
            <h3 class="text-center my-4">Track Another Parcel</h3>
            <p>
                <?php
                    if(isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ){
                        echo '<p class="alert-danger">' ;
                            foreach($_SESSION['ERRMSG_ARR'] as $msg) {
                                echo tracking_escape($msg);
                            }
                        echo '</p>';
                        unset($_SESSION['ERRMSG_ARR']);
                    }
                ?>
            </p><br>
            <div class="w-100 tracking-form mx-auto border">
                <h6 class="text-center my-4">Enter Tracking Number</h6> <br />
                <form action="../controllers/tracking_exec.php" method="POST" name="tracking" class="form-inline d-flex justify-content-center mb-4">
                    <input type="text" class="form-control mb-2 mr-sm-2" name ="trackingnumber" id="inlineFormInputName2" placeholder="001234567" required>
                    <button type="submit" class="btn btn-primary mb-2">Track</button>
                </form>
            </div>
      </div>
    </div>
</div>
<?php
  include_once("../views/map.php");
  require_once('footer.php')
?>