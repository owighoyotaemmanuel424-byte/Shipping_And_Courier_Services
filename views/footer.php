<?php
$footerPagePrefix = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/views/') !== false ? '' : 'views/';
?>
<section class="footer-widget-section">
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
				<div class="footer-widget">
					<h3>About</h3>
					<p>NEXTGEN Logistics is a representative logistics operator providing full range of service in the sphere of customs cargo and
					transportation worldwide.
					</p>
					<br>

					<address>
						<strong>Customer support</strong>
						<br>
						<a href="<?php echo $footerPagePrefix; ?>contact.php">Contact our team</a>
						<br>
						<br>
						<span class="map-marker">Pickup and delivery coverage depends on your route.</span>
					</address>

					
				</div><!-- /.footer-widget -->
			</div><!-- /.col-sm-4 -->

			<div class="col-sm-4">
				<div class="footer-widget">
					<h3>Useful Links</h3>
					<div class="row">
						<div class="col-sm-6">
							<ul class="quick-links">
								<li><a href="<?php echo $footerPagePrefix; ?>about.php">About us</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>tracking.php">Track a shipment</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>contact.php">Contact</a></li>
								<li><a href="#">Career</a></li>
								<li><a href="#">Staffs</a></li>
								<li><a href="#">News</a></li>
								<li><a href="#">Support</a></li>
							</ul>
						</div>
						<div class="col-sm-6">
							<ul class="quick-links">
								<li><a href="<?php echo $footerPagePrefix; ?>services.php">Our services</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>quotation.php">Request a quote</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>contact.php">Customer support</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>about.php">Why NEXTGEN</a></li>
								<li><a href="<?php echo $footerPagePrefix; ?>services.php">Freight services</a></li>
							</ul>
						</div>
					</div>
				</div><!-- /.footer-widget -->
			</div><!-- /.col-md-4 -->

			<div class="col-sm-4">
				<div class="footer-widget">
					<h3>Stay in Touch</h3>
					<p>Enter your email address to receive news &amp; offers from us</p>

					<form class="newsletter-form">
						<div class="form-group">
							<label class="sr-only" for="InputEmail1">Email address</label>
							<input type="email" class="form-control" id="InputEmail1" placeholder="Your email address">
							<button type="submit" class="btn">Send &nbsp;<i class="fa fa-angle-right"></i></button>
						</div>
					</form>		
					<ul class="social-links list-inline">
						<li class="list-inline-item"><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
						<li class="list-inline-item"><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
						<li class="list-inline-item"><a class="google-plus" href="#"><i class="fa fa-google-plus"></i></a></li>
						<li class="list-inline-item"><a class="linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>
					</ul>        				
				</div><!-- /.footer-widget -->
			</div><!-- /.col-md-4 -->
		</div><!-- /.row -->
	</div><!-- /.container -->
</section>
<footer class="copyright-section">
  <div class="container">
    <div class="footer-menu">
      <ul>
        <li><a href="#">Privacy &amp; Cookies</a></li>
        <li><a href="#">Terms &amp; Conditions</a></li>
        <li><a href="#">Accessibility</a></li>
      </ul>
    </div>

    <div class="copyright-info">
      <span>Copyright © <?php echo date("Y"); ?> NEXTGEN Logistics. All rights reserved.<br></span>
    </div>
  </div><!-- /.container -->
</footer>

<!--- Footer --->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>