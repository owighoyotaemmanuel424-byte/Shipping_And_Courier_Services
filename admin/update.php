<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/../database/config.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    header('Location: dashboard.php');
    exit();
}
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (!$db) {
    error_log('Update shipment database connection failed: ' . mysqli_connect_error());
    $_SESSION['ERRMSG_ARR'] = ['Unable to update shipment right now. Please try again.'];
    header('Location: dashboard.php');
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
    $optional = in_array($key, ['content', 'szip', 'czip', 'dzip'], true);
    if ((!$optional && $value === '') || strlen($value) > $maxLength) {
        $_SESSION['ERRMSG_ARR'] = ['Please complete all shipment fields using valid values.'];
        mysqli_close($db);
        header('Location: edit.php?id=' . $id);
        exit();
    }
    $data[] = $value;
}
if (!in_array($data[3], ['Air Freight', 'Marine Freight', 'Land Freight'], true) || !in_array($data[17], ['Shipped', 'Delivered', 'Transit', 'Packaging', 'Missing Parcel'], true)) {
    $_SESSION['ERRMSG_ARR'] = ['Choose a valid shipment type and parcel status.'];
    mysqli_close($db);
    header('Location: edit.php?id=' . $id);
    exit();
}
if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $data[5]) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $data[6]) || !checkdate((int)substr($data[5], 5, 2), (int)substr($data[5], 8, 2), (int)substr($data[5], 0, 4)) || !checkdate((int)substr($data[6], 5, 2), (int)substr($data[6], 8, 2), (int)substr($data[6], 0, 4))) {
    $_SESSION['ERRMSG_ARR'] = ['Enter valid shipment and delivery dates.'];
    mysqli_close($db);
    header('Location: edit.php?id=' . $id);
    exit();
}
if (strcmp($data[6], $data[5]) < 0) {
    $_SESSION['ERRMSG_ARR'] = ['Delivery date cannot be earlier than the shipment date.'];
    mysqli_close($db);
    header('Location: edit.php?id=' . $id);
    exit();
}
if (!preg_match('/^[A-Za-z0-9_-]+$/', $data[2])) {
    $_SESSION['ERRMSG_ARR'] = ['Tracking numbers may contain only letters, numbers, hyphens, and underscores.'];
    mysqli_close($db);
    header('Location: edit.php?id=' . $id);
    exit();
}
$sql = 'UPDATE tracking SET first_name=?,last_name=?,tracking_number=?,shipment_type=?,content=?,shipment_date=?,delivery_date=?,source_city=?,source_state=?,source_zip=?,current_city=?,current_state=?,current_zip=?,destination_city=?,destination_state=?,destination_zip=?,contact_number=?,parcel_status=? WHERE id=?';
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    error_log('Update shipment query preparation failed: ' . mysqli_error($db));
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = ['Unable to update shipment right now. Please try again.'];
    header('Location: edit.php?id=' . $id);
    exit();
}
$types = 'ssssssssssssssssssi';
mysqli_stmt_bind_param($stmt, $types, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[15], $data[16], $data[17], $id);
if (!mysqli_stmt_execute($stmt)) {
    $duplicate = mysqli_stmt_errno($stmt) === 1062;
    error_log('Update shipment failed with database error ' . mysqli_stmt_errno($stmt));
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = [$duplicate ? 'That tracking number already exists. Please use a unique number.' : 'Unable to update shipment right now. Please try again.'];
    header('Location: edit.php?id=' . $id);
    exit();
}
mysqli_stmt_close($stmt);
mysqli_close($db);
header('Location: dashboard.php');
exit();
