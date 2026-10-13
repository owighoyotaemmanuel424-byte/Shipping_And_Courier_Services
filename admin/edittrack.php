<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/../database/config.php';
$db_login = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (!$db_login) {
    error_log('Edit shipment database connection failed: ' . mysqli_connect_error());
    http_response_code(503);
    exit('Shipment details are temporarily unavailable.');
}
mysqli_set_charset($db_login, 'utf8mb4');
$stmt = mysqli_prepare($db_login, 'SELECT * FROM tracking WHERE id = ? LIMIT 1');
if (!$stmt) {
    error_log('Edit shipment query preparation failed: ' . mysqli_error($db_login));
    mysqli_close($db_login);
    http_response_code(503);
    exit('Shipment details are temporarily unavailable.');
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);
mysqli_close($db_login);
if (!$row) {
    header('Location: dashboard.php');
    exit();
}
$id = (int)$row['id'];
$num = $row['tracking_number'];
$shipdate = $row['shipment_date'];
$estimate_date = $row['delivery_date'];
$ship_type = $row['shipment_type'];
$content = $row['content'];
$r_firstname = $row['first_name'];
$r_lastname = $row['last_name'];
$source_city = $row['source_city'];
$source_state = $row['source_state'];
$source_country = $row['source_zip'];
$current_city = $row['current_city'];
$current_state = $row['current_state'];
$current_country = $row['current_zip'];
$destination_city = $row['destination_city'];
$destination_state = $row['destination_state'];
$destination_country = $row['destination_zip'];
$phone = $row['contact_number'];
$status = $row['parcel_status'];
?>
