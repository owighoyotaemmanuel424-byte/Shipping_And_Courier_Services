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
    <title>NEXTGEN - Contact Us</title>
  </head>
  <body>
<?php
    require('header.php');
?>
<section class="page-title-secttion">
    <div class="container">
      <div class="page-header">
        <h1>Contact</h1>
      </div>
      <ol class="breadcrumb justify-content-center">
        <li><a href="../index.php">Home</a></li>
        <li class="active">/ Contact</li>
      </ol>
    </div>
</section>
<?php
    include_once("map.php");
?>
<section class="contact-wrap section-padding">
  <div class="container">
    <div class="section-heading">
      <h2 class="section-title">How can we help?</h2>
      <p>Choose the next step that best matches your shipment. Service availability and delivery estimates are confirmed for each route.</p>
    </div>
    <div class="row">
      <div class="col-12 col-md-4 mb-4">
        <article class="office-address h-100">
          <h3>Request a quote</h3>
          <p>Share your pickup and delivery locations, shipment details and preferred dates so the team can review your request.</p>
          <a class="btn btn-primary" href="quotation.php">Get a shipping quote</a>
        </article>
      </div>
      <div class="col-12 col-md-4 mb-4">
        <article class="office-address h-100">
          <h3>Track a shipment</h3>
          <p>Use the tracking number provided for your shipment to view its available tracking details.</p>
          <a class="btn btn-primary" href="tracking.php">Track shipment</a>
        </article>
      </div>
      <div class="col-12 col-md-4 mb-4">
        <article class="office-address h-100">
          <h3>Explore services</h3>
          <p>Review available freight options and contact the team to confirm what is supported for your route.</p>
          <a class="btn btn-primary" href="services.php">View services</a>
        </article>
      </div>
    </div>
  </div>
</section>
<?php
    require_once('footer.php')
?>