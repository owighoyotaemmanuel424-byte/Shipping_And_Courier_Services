<?php
/**
 * Shared PDO SQLite connection. Tables are initialized on first connection.
 */
require_once __DIR__ . '/config.php';

function app_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $directory = dirname(SQLITE_PATH);
    if (!is_dir($directory) || !is_writable($directory)) {
        error_log('SQLite storage directory is missing or not writable: ' . $directory);
        throw new RuntimeException('Persistent database storage is unavailable.');
    }

    try {
        $pdo = new PDO('sqlite:' . SQLITE_PATH, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
            adminid INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            passw TEXT NOT NULL
        )");
        $pdo->exec("CREATE TABLE IF NOT EXISTS tracking (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            tracking_number TEXT NOT NULL UNIQUE,
            shipment_type TEXT NOT NULL,
            content TEXT NOT NULL DEFAULT '',
            shipment_date TEXT NOT NULL,
            delivery_date TEXT NOT NULL,
            source_city TEXT NOT NULL,
            source_state TEXT NOT NULL,
            source_zip TEXT NOT NULL DEFAULT '',
            current_city TEXT NOT NULL,
            current_state TEXT NOT NULL,
            current_zip TEXT NOT NULL DEFAULT '',
            destination_city TEXT NOT NULL,
            destination_state TEXT NOT NULL,
            destination_zip TEXT NOT NULL DEFAULT '',
            contact_number TEXT NOT NULL,
            parcel_status TEXT NOT NULL DEFAULT 'Packaging',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_login_attempts (
            ip_hash TEXT PRIMARY KEY,
            attempts INTEGER NOT NULL DEFAULT 0,
            window_started_at INTEGER NOT NULL,
            locked_until INTEGER NOT NULL DEFAULT 0
        )");
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_tracking_status ON tracking(parcel_status)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_tracking_delivery_date ON tracking(delivery_date)');
        return $pdo;
    } catch (Throwable $exception) {
        error_log('SQLite connection/initialization failed: ' . $exception->getMessage());
        throw new RuntimeException('Database is temporarily unavailable.', 0, $exception);
    }
}

// Existing pages are being migrated to use this PDO connection.
try {
    $db_login = app_db();
} catch (Throwable $exception) {
    $db_login = null;
}
