<?php
/**
 * SQLite configuration for Railway persistent storage.
 * Set SQLITE_PATH=/data/nextgen.sqlite and mount a Railway volume at /data.
 */
if (!defined('SQLITE_PATH')) {
    $configuredPath = getenv('SQLITE_PATH');
    define('SQLITE_PATH', ($configuredPath !== false && trim($configuredPath) !== '') ? $configuredPath : '/data/nextgen.sqlite');
}
