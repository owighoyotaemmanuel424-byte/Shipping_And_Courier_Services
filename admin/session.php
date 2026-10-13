<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

require_once __DIR__ . '/../database/config.php';

if (empty($_SESSION['userId']) || empty($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$db) {
    error_log('Admin session validation database connection failed: ' . mysqli_connect_error());
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit();
}
mysqli_set_charset($db, 'utf8mb4');

$stmt = mysqli_prepare($db, 'SELECT adminid, username FROM admin WHERE adminid = ? AND username = ? LIMIT 1');
if (!$stmt) {
    error_log('Admin session validation query preparation failed: ' . mysqli_error($db));
    mysqli_close($db);
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit();
}
$userId = (int)$_SESSION['userId'];
$username = (string)$_SESSION['username'];
mysqli_stmt_bind_param($stmt, 'is', $userId, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);
mysqli_close($db);

if (!$admin) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit();
}
$login_session = $admin['username'];
