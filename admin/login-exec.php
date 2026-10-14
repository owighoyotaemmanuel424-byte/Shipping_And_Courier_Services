<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'secure' => $isHttps, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}
require_once __DIR__ . '/../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST'); http_response_code(405); exit('Method not allowed');
}
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    $_SESSION['ERRMSG_ARR'] = ['Your login session expired. Please try again.']; header('Location: index.php'); exit();
}
$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');
if ($username === '' || $password === '' || strlen($username) > 190 || strlen($password) > 4096) {
    $_SESSION['ERRMSG_ARR'] = ['Enter your username and password.']; header('Location: index.php'); exit();
}
if (!$db_login instanceof PDO) {
    $_SESSION['ERRMSG_ARR'] = ['Login is temporarily unavailable. Please try again later.']; header('Location: index.php'); exit();
}
try {
    $stmt = $db_login->prepare('SELECT adminid, username, passw FROM admin WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch();
} catch (Throwable $exception) {
    error_log('Admin login query failed: ' . $exception->getMessage());
    $_SESSION['ERRMSG_ARR'] = ['Login is temporarily unavailable. Please try again later.']; header('Location: index.php'); exit();
}
$authenticated = false;
if ($row && isset($row['passw'])) {
    $storedPassword = (string)$row['passw'];
    if (password_verify($password, $storedPassword)) {
        $authenticated = true;
    } elseif (hash_equals($storedPassword, $password)) {
        $authenticated = true;
        try {
            $upgrade = $db_login->prepare('UPDATE admin SET passw = :passw WHERE adminid = :adminid');
            $upgrade->execute([':passw' => password_hash($password, PASSWORD_DEFAULT), ':adminid' => (int)$row['adminid']]);
        } catch (Throwable $exception) {
            error_log('Legacy admin password upgrade failed: ' . $exception->getMessage());
        }
    }
}
if (!$authenticated) {
    $_SESSION['ERRMSG_ARR'] = ['Invalid username or password.']; header('Location: index.php'); exit();
}
session_regenerate_id(true);
$_SESSION['userId'] = (int)$row['adminid'];
$_SESSION['username'] = $row['username'];
unset($_SESSION['ERRMSG_ARR']);
header('Location: dashboard.php');
exit();
