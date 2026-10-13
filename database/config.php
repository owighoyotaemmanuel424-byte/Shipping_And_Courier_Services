<?php
/**
 * Database configuration.
 * Preferred: set DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE and optionally DB_PORT.
 * Railway MySQL variables are supported as fallbacks.
 */
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: 'localhost'));
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root'));
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : ''));
}
if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', getenv('DB_DATABASE') ?: (getenv('MYSQLDATABASE') ?: 'nextgen'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', (int)(getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: 3306)));
}
mysqli_report(MYSQLI_REPORT_OFF);
