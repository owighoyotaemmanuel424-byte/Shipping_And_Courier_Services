<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/../database/config.php';

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (!$db) {
    error_log('Create shipment database connection failed: ' . mysqli_connect_error());
    $_SESSION['ERRMSG_ARR'] = ['Unable to save shipment right now. Please try again.'];
    header('Location: add-tracking.php');
    exit();
}
mysqli_set_charset($db, 'utf8mb4');

$fields = [
    'firstname' => 190, 'lastname' => 190, 'tracknum' => 100,
    'shipmentType' => 100, 'content' => 2000, 'sdate' => 30, 'ddate' => 30,
    'scity' => 190, 'sstate' => 190, 'szip' => 40, 'ccity' => 190, 'cstate' => 190,
    'czip' => 40, 'dcity' => 190, 'dstate' => 190, 'dzip' => 40,
    'contactnumber' => 60, 'status' => 100
];
$data = [];
foreach ($fields as $key => $maxLength) {
    $value = trim((string)($_POST[$key] ?? ''));
    if ($value === '' || strlen($value) > $maxLength) {
        $_SESSION['ERRMSG_ARR'] = ['Please complete all shipment fields using valid values.'];
        mysqli_close($db);
        header('Location: add-tracking.php');
        exit();
    }
    $data[] = $value;
}
if (!preg_match('/^[A-Za-z0-9_-]+$/', $data[2])) {
    $_SESSION['ERRMSG_ARR'] = ['Tracking numbers may contain only letters, numbers, hyphens, and underscores.'];
    mysqli_close($db);
    header('Location: add-tracking.php');
    exit();
}

$sql = 'INSERT INTO tracking (first_name,last_name,tracking_number,shipment_type,content,shipment_date,delivery_date,source_city,source_state,source_zip,current_city,current_state,current_zip,destination_city,destination_state,destination_zip,contact_number,parcel_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    error_log('Create shipment query preparation failed: ' . mysqli_error($db));
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = ['Unable to save shipment right now. Please try again.'];
    header('Location: add-tracking.php');
    exit();
}
mysqli_stmt_bind_param($stmt, 'ssssssssssssssssss', ...$data);
if (!mysqli_stmt_execute($stmt)) {
    $duplicate = mysqli_stmt_errno($stmt) === 1062;
    error_log('Create shipment failed with database error ' . mysqli_stmt_errno($stmt));
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = [$duplicate ? 'That tracking number already exists. Please use a unique number.' : 'Unable to save shipment right now. Please try again.'];
    header('Location: add-tracking.php');
    exit();
}
mysqli_stmt_close($stmt);
mysqli_close($db);
header('Location: dashboard.php');
exit();
