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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed');
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');
if ($username === '' || $password === '' || strlen($username) > 190 || strlen($password) > 4096) {
    $_SESSION['ERRMSG_ARR'] = ['Enter your username and password.'];
    header('Location: index.php');
    exit();
}

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (!$db) {
    error_log('Admin login database connection failed: ' . mysqli_connect_error());
    $_SESSION['ERRMSG_ARR'] = ['Login is temporarily unavailable. Please try again later.'];
    header('Location: index.php');
    exit();
}
mysqli_set_charset($db, 'utf8mb4');

$stmt = mysqli_prepare($db, 'SELECT adminid, username, passw FROM admin WHERE username = ? LIMIT 1');
if (!$stmt) {
    error_log('Admin login query preparation failed: ' . mysqli_error($db));
    mysqli_close($db);
    $_SESSION['ERRMSG_ARR'] = ['Login is temporarily unavailable. Please try again later.'];
    header('Location: index.php');
    exit();
}
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

$authenticated = false;
if ($row && isset($row['passw'])) {
    $storedPassword = (string)$row['passw'];
    // Support existing legacy plaintext records once, then upgrade them to password_hash().
    if (password_verify($password, $storedPassword)) {
        $authenticated = true;
    } elseif (hash_equals($storedPassword, $password)) {
        $authenticated = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upgrade = mysqli_prepare($db, 'UPDATE admin SET passw = ? WHERE adminid = ?');
        if ($upgrade) {
            mysqli_stmt_bind_param($upgrade, 'si', $newHash, $row['adminid']);
            mysqli_stmt_execute($upgrade);
            mysqli_stmt_close($upgrade);
        }
    }
}
mysqli_close($db);

if (!$authenticated) {
    // Avoid revealing whether the username exists.
    $_SESSION['ERRMSG_ARR'] = ['Invalid username or password.'];
    header('Location: index.php');
    exit();
}

session_regenerate_id(true);
$_SESSION['userId'] = (int)$row['adminid'];
$_SESSION['username'] = $row['username'];
unset($_SESSION['ERRMSG_ARR']);
header('Location: dashboard.php');
exit();
