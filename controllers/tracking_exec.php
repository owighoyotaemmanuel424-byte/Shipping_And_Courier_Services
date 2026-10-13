<?php
session_start();

require_once __DIR__ . '/../database/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed');
}

$trackingNumber = trim((string)($_POST['trackingnumber'] ?? ''));
if ($trackingNumber === '' || strlen($trackingNumber) > 100 || !preg_match('/^[A-Za-z0-9_-]+$/', $trackingNumber)) {
    $_SESSION['ERRMSG_ARR'] = ['Enter a valid tracking number.'];
    header('Location: ../views/tracking.php');
    exit();
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$conn) {
    error_log('Tracking lookup database connection failed: ' . mysqli_connect_error());
    $_SESSION['ERRMSG_ARR'] = ['Tracking is temporarily unavailable. Please try again shortly.'];
    header('Location: ../views/tracking.php');
    exit();
}
mysqli_set_charset($conn, 'utf8mb4');

$stmt = mysqli_prepare($conn, 'SELECT tracking_number FROM tracking WHERE tracking_number = ? LIMIT 1');
if (!$stmt) {
    error_log('Tracking lookup prepare failed: ' . mysqli_error($conn));
    $_SESSION['ERRMSG_ARR'] = ['Tracking is temporarily unavailable. Please try again shortly.'];
    mysqli_close($conn);
    header('Location: ../views/tracking.php');
    exit();
}
mysqli_stmt_bind_param($stmt, 's', $trackingNumber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$shipment = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($shipment) {
    session_regenerate_id(true);
    $_SESSION['USER_NUM'] = $shipment['tracking_number'];
    header('Location: ../views/tracking_details.php');
    exit();
}

$_SESSION['ERRMSG_ARR'] = ['We could not find that tracking number. Please check it and try again.'];
header('Location: ../views/tracking.php');
exit();
