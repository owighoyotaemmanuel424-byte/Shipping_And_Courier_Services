<?php
/**
 * Database configuration.
 * Set DB_HOST, DB_USER, DB_PASSWORD and DB_DATABASE in the hosting environment.
 * Local defaults are retained for the original development setup.
 */
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') !== false ? getenv('DB_USER') : 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');
}
if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', getenv('DB_DATABASE') !== false ? getenv('DB_DATABASE') : 'nextgen');
}

mysqli_report(MYSQLI_REPORT_OFF);
