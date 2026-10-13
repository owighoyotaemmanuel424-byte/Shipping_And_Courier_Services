<?php
require_once __DIR__ . '/config.php';
$db_login = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PORT);
if ($db_login) {
    mysqli_set_charset($db_login, 'utf8mb4');
} else {
    error_log('Database connection failed: ' . mysqli_connect_error());
}
