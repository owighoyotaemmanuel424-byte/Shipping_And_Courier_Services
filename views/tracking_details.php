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
        <section class="tracking-progress-card" aria-labelledby="trackingProgressTitle">
          <p class="tracking-progress-kicker">LIVE SHIPMENT OVERVIEW</p>
          <h3 id="trackingProgressTitle">Delivery progress</h3>
          <p class="tracking-progress-number">Tracking ID: <strong><?php echo tracking_escape($num); ?></strong></p>
          <?php if ($status === 'Missing Parcel'): ?>
            <div class="tracking-alert" role="status"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i><div><strong>Shipment requires attention</strong><p>Please contact customer support for the latest information about this parcel.</p></div></div>
          <?php else:
            $trackingStages = ['Packaging' => 'Shipment prepared', 'Shipped' => 'Shipment dispatched', 'Transit' => 'In transit', 'Delivered' => 'Delivered'];
            $stageOrder = ['Packaging', 'Shipped', 'Transit', 'Delivered'];
            $activeStage = array_search($status, $stageOrder, true);
            if ($activeStage === false) $activeStage = 0;
          ?>
            <ol class="tracking-timeline">
              <?php foreach ($stageOrder as $stageIndex => $stageName): $stageDone = $stageIndex <= $activeStage; ?>
                <li class="<?php echo $stageDone ? 'is-complete' : ''; ?> <?php echo $stageIndex === $activeStage ? 'is-current' : ''; ?>">
                  <span class="tracking-timeline-marker" aria-hidden="true"><?php if ($stageDone): ?><i class="fa-solid fa-check"></i><?php else: ?><span><?php echo $stageIndex + 1; ?></span><?php endif; ?></span>
                  <div class="tracking-timeline-copy"><strong><?php echo tracking_escape($trackingStages[$stageName]); ?></strong>
                    <?php if ($stageIndex === 0): ?><span>Shipment information recorded</span>
                    <?php elseif ($stageIndex === 1): ?><span>Parcel has left the origin</span>
                    <?php elseif ($stageIndex === 2): ?><span>Parcel is moving toward its destination</span>
                    <?php else: ?><span>Shipment delivery completed</span><?php endif; ?>
                  </div>
                  <?php if ($stageIndex === $activeStage): ?><span class="tracking-current-badge">Current</span><?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ol>
          <?php endif; ?>
          <div class="tracking-route-summary">
            <div><span>ORIGIN</span><strong><?php echo tracking_escape($row['source_city']); ?></strong><small><?php echo tracking_escape(trim($row['source_state'] . ' ' . $row['source_zip'])); ?></small></div>
            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            <div><span>DESTINATION</span><strong><?php echo tracking_escape($row['destination_city']); ?></strong><small><?php echo tracking_escape(trim($row['destination_state'] . ' ' . $row['destination_zip'])); ?></small></div>
          </div>
          <div class="tracking-delivery-date"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i><div><span>Estimated delivery</span><strong><?php echo tracking_escape($estimate_date); ?></strong></div></div>
        </section>
      </div>
      <div class="col-12 col-md-6">
      <div class="col-12"><section class="shipment-map-card" aria-labelledby="shipmentMapTitle">
<div class="shipment-map-heading"><div><p class="shipment-map-kicker">SHIPMENT ROUTE</p><h3 id="shipmentMapTitle">Track your parcel on the map</h3><p>Route overview based on shipment locations recorded by NEXTGEN.</p></div>
<div class="shipment-map-controls" role="group" aria-label="Map layer"><button type="button" class="is-active" id="shipmentStreetLayer">Street</button><button type="button" id="shipmentSatelliteLayer">Satellite</button></div></div>
<div id="shipmentTrackingMap" class="shipment-tracking-map" role="application" aria-label="Interactive shipment route map"></div>
<div class="shipment-map-legend"><span><i class="shipment-map-dot shipment-origin-dot"></i>Origin</span><span><i class="shipment-map-dot shipment-current-dot"></i>Recorded current location</span><span><i class="shipment-map-dot shipment-destination-dot"></i>Destination</span></div>
<p class="shipment-map-note" id="shipmentMapStatus" role="status">Preparing route map…</p>
<p class="shipment-map-disclaimer">Map pins are estimated from saved city/state fields. This is not live GPS tracking; real-time courier position requires connected GPS coordinates.</p></section></div>
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
                    <input type="text" class="form-control mb-2 mr-sm-2" name="trackingnumber" id="inlineFormInputName2" maxlength="100" pattern="[A-Za-z0-9_-]+" autocomplete="off" placeholder="Enter tracking ID" required>
                    <button type="submit" class="btn btn-primary mb-2">Track</button>
                </form>
            </div>
      </div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function(){const el=document.getElementById('shipmentTrackingMap');if(!el||!window.L)return;
const places=[{key:'origin',label:'Origin',address:<?php echo json_encode(trim($row['source_city'] . ' ' . $row['source_state'] . ' ' . $row['source_zip']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>},{key:'current',label:'Recorded current location',address:<?php echo json_encode(trim($row['current_city'] . ' ' . $row['current_state'] . ' ' . $row['current_zip']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>},{key:'destination',label:'Destination',address:<?php echo json_encode(trim($row['destination_city'] . ' ' . $row['destination_state'] . ' ' . $row['destination_zip']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>}].filter(p=>p.address&&p.address.trim());
const status=document.getElementById('shipmentMapStatus'),map=L.map(el,{scrollWheelZoom:false}).setView([20,0],2);
const street=L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'&copy; OpenStreetMap contributors &copy; CARTO',subdomains:'abcd',maxZoom:20});
const satellite=L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{attribution:'Tiles &copy; Esri',maxZoom:19});street.addTo(map);
const streetBtn=document.getElementById('shipmentStreetLayer'),satBtn=document.getElementById('shipmentSatelliteLayer');
streetBtn.addEventListener('click',function(){map.removeLayer(satellite);street.addTo(map);this.classList.add('is-active');satBtn.classList.remove('is-active')});
satBtn.addEventListener('click',function(){map.removeLayer(street);satellite.addTo(map);this.classList.add('is-active');streetBtn.classList.remove('is-active')});
const styles={origin:['#1769aa','O'],current:['#e59b21','C'],destination:['#15966a','D']},points=[];
async function lookup(address){const r=await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q='+encodeURIComponent(address),{headers:{Accept:'application/json'}});if(!r.ok)throw Error('Geocoding failed');const a=await r.json();return a.length?[+a[0].lat,+a[0].lon]:null}
async function renderRoute(){if(!places.length){status.textContent='Shipment addresses are unavailable.';return}let found=0;
for(let i=0;i<places.length;i++){const p=places[i];try{const point=await lookup(p.address);if(point){const s=styles[p.key],icon=L.divIcon({className:'shipment-map-marker-wrap',html:'<span class="shipment-map-marker" style="--marker-color:'+s[0]+'">'+s[1]+'</span>',iconSize:[34,34],iconAnchor:[17,17]});L.marker(point,{icon}).addTo(map).bindPopup('<strong>'+p.label+'</strong><br>'+p.address.replace(/[&<>"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch])));points.push(point);found++}}catch(e){console.warn('Shipment location map lookup failed',e)}if(i<places.length-1)await new Promise(r=>setTimeout(r,1100))}
if(points.length>1){L.polyline(points,{color:'#1769aa',weight:4,opacity:.8,dashArray:'8 8'}).addTo(map);map.fitBounds(L.latLngBounds(points).pad(.18),{maxZoom:8})}else if(points.length===1)map.setView(points[0],8);
status.textContent=found?'Mapped '+found+' of '+places.length+' shipment locations.':'Could not locate the saved addresses. Check city/state details.'}renderRoute()})();
</script>

<?php require_once('footer.php'); ?>