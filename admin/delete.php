<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'secure' => $isHttps, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}
if (empty($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed');
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/../database/config.php';
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (!$db) {
    error_log('Delete shipment database connection failed: ' . mysqli_connect_error());
    $_SESSION['ERRMSG_ARR'] = ['Unable to delete shipment right now. Please try again.'];
    header('Location: dashboard.php');
    exit();
}
$stmt = mysqli_prepare($db, 'DELETE FROM tracking WHERE id = ?');
if (!$stmt) {
    error_log('Delete shipment query preparation failed: ' . mysqli_error($db));
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = ['Unable to delete shipment right now. Please try again.'];
    header('Location: dashboard.php');
    exit();
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($db);
header('Location: dashboard.php');
exit();
