<?php
$quoteStatus = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $fields = [
        'firstname' => 'First name', 'lastname' => 'Last name', 'email' => 'Email',
        'contactnumber' => 'Contact number', 'paddress' => 'Pickup address',
        'daddress' => 'Delivery address', 'pdate' => 'Pickup date', 'ptime' => 'Pickup time',
        'ddate' => 'Expected delivery date', 'pcity' => 'Pickup city', 'pstate' => 'Pickup state',
        'pcountry' => 'Pickup country', 'dcity' => 'Delivery city', 'dstate' => 'Delivery state',
        'dcountry' => 'Delivery country', 'receivername' => 'Receiver name',
        'shipmentType' => 'Shipment type', 'pickupType' => 'Pickup vehicle', 'content' => 'Parcel contents'
    ];
    $values = [];
    foreach ($fields as $key => $label) {
        $value = isset($_POST[$key]) && is_string($_POST[$key]) ? trim(strip_tags($_POST[$key])) : '';
        $values[$key] = str_replace(["\r", "\n"], ' ', $value);
    }
    if ($values['firstname'] === '' || $values['lastname'] === '' ||
        !filter_var($values['email'], FILTER_VALIDATE_EMAIL) ||
        $values['contactnumber'] === '' || $values['paddress'] === '' || $values['daddress'] === '') {
        $quoteStatus = 'Please complete the required fields and provide a valid email address.';
    } elseif ($values['pdate'] !== '' && $values['ddate'] !== '' && $values['ddate'] < $values['pdate']) {
        $quoteStatus = 'Expected delivery date cannot be earlier than pickup date.';
    } else {
        $recipient = getenv('QUOTE_RECIPIENT_EMAIL') ?: '';
        $from = getenv('QUOTE_FROM_EMAIL') ?: '';
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $quoteStatus = 'Quote requests are temporarily unavailable. Please contact the site administrator.';
            error_log('Quote request email is not configured; set QUOTE_RECIPIENT_EMAIL and QUOTE_FROM_EMAIL.');
        } else {
            $lines = [];
            foreach ($fields as $key => $label) {
                $lines[] = $label . ': ' . ($values[$key] !== '' ? $values[$key] : 'Not provided');
            }
            $headers = "From: " . $from . "\r\n" . "Reply-To: " . $values['email'] . "\r\n" . "Content-Type: text/plain; charset=UTF-8";
            if (mail($recipient, 'NEXTGEN shipping quote request', implode("\n", $lines), $headers)) {
                $quoteStatus = 'Your quote request was sent successfully. Our team will review the details.';
            } else {
                $quoteStatus = 'We could not send your request right now. Please try again later.';
                error_log('NEXTGEN quote request email could not be sent.');
            }
        }
    }
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
    <title>NEXTGEN - Ask A Quote</title>
  </head>
  <body>
<?php
    require('header.php');
?>
<section class="page-title-secttion">
    <div class="container">
      <div class="page-header">
        <h1>Request a Quote</h1>
      </div>
      <ol class="breadcrumb justify-content-center">
        <li><a href="#">Home </a></li>
        <li class="active">/ Quote</li>
      </ol>
    </div>
</section>
<div class="container my-4">
  <div class="row">
  <div class="col-12 col-md-9">
    <h2>Request a quote!</h2>
    <?php if ($quoteStatus !== '') : ?><div class="alert alert-info" role="status"><?php echo htmlspecialchars($quoteStatus, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div><?php endif; ?>
    <div class="">
    <form method="POST" action="quotation.php">
      <div class="form-row">
        <div class="col-12 col-md-6 mb-3">
          <label for="validationDefault01">First name</label>
          <input type="text" name ="firstname" class="form-control" id="validationDefault01" placeholder="Mark" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="validationDefault02">Last name</label>
          <input type="text" name ="lastname" class="form-control" id="validationDefault02" placeholder="Otto" required>
        </div>
      </div>
      <div class="form-row">
        <div class="col-12 col-md-6 mb-3">
          <label>Email Address</label>
          <input type="email" name ="email" class="form-control" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label>Contact Number</label>
          <input type="text" name ="contactnumber" class="form-control" required>
        </div>
      </div>
      <div class="form-row">
        <div class="col-12 col-md-6 mb-3">
          <label for="validationDefault01">Pickup Address</label>
          <input type="text" name ="paddress" class="form-control" id="validationDefault01" placeholder="Oslo, 12 Street" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="validationDefault02">Delivery Address</label>
          <input type="text" name ="daddress" class="form-control" id="validationDefault02" placeholder="Port Harcort" required>
        </div>
      </div>
      <div class="form-row">
        <div class="col-12 col-md-4 mb-3">
          <label for="validationDefault01">Pick Up Date</label>
          <input type="date" name ="pdate" class="form-control" required>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <label for="validationDefault01">Pick Up Time</label>
          <input type="time" name ="ptime" class="form-control" required>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <label for="validationDefault02">Expected Delivery Date</label>
          <input type="date" name ="ddate" class="form-control" required>
        </div>
      </div>
      <div class="form-row">
        <div class="col-md-6 mb-3">
          <label for="validationDefault03">Pick Up City</label>
          <input type="text" name ="pcity" class="form-control" id="validationDefault03" placeholder="City" required>
        </div>
        <div class="col-md-3 mb-3">
          <label for="validationDefault04">Pick Up State</label>
          <input type="text" name ="pstate" class="form-control" id="validationDefault04" placeholder="State" required>
        </div>
        <div class="col-md-3 mb-3">
          <label for="validationDefault05">Pick Up Country</label>
          <input type="text" name ="pcountry" class="form-control" id="validationDefault05" placeholder="Country">
        </div>
      </div>
      <div class="form-row">
        <div class="col-md-6 mb-3">
          <label for="validationDefault03">Destination City</label>
          <input type="text" name ="dcity" class="form-control" id="validationDefault03" placeholder="City" required>
        </div>
        <div class="col-md-3 mb-3">
          <label for="validationDefault04">Destination State</label>
          <input type="text" name ="dstate" class="form-control" id="validationDefault04" placeholder="State" required>
        </div>
        <div class="col-md-3 mb-3">
          <label for="validationDefault05">Destination Country</label>
          <input type="text" name ="dcountry" class="form-control" id="validationDefault05" placeholder="Country">
        </div>
      </div>
      <div class="form-row">
        <div class="col-12 col-md-4 mb-3">
          <label for="validationDefault01">Receiver's Name</label>
          <input type="text" name ="receivername" class="form-control" id="validationDefault01" placeholder="John Doe" required>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <label class="mr-sm-2" for="inlineFormCustomSelect">Shipment Type</label>
          <select class="custom-select mr-sm-2" name ="shipmentType" id="inlineFormCustomSelect">
            <option selected>Choose...</option>
            <option value="Air Freight">Air Freight</option>
            <option value="Marine Freight">Shipping</option>
            <option value="Land Freight">Land freight</option>
          </select>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <label class="mr-sm-2" for="inlineFormCustomSelect">Pick Up Vehicle Type</label>
          <select class="custom-select mr-sm-2" name ="pickupType" id="inlineFormCustomSelect">
            <option selected>Choose...</option>
            <option value="Air Freight">Bike</option>
            <option value="Marine Freight">Van</option>
            <option value="Land Freight">Truck</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <label for="exampleFormControlTextarea1">Parcel Content</label>
        <textarea class="form-control" name ="content" id="exampleFormControlTextarea1" rows="3"></textarea>
      </div>
      <div class="form-row mt-3">
        <input class="btn btn-primary" name="submit" type="submit" value="Submit Form"/>
      </div>
    </form>
    </div>
  </div>
  </div>
</div>
<?php
    include_once("map.php");
    require_once('footer.php')
?>