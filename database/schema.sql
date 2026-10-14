-- NEXTGEN Logistics SQLite schema
-- Database file: /data/nextgen.sqlite (configure SQLITE_PATH to override)

CREATE TABLE IF NOT EXISTS admin (
    adminid INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    passw TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS tracking (
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
);

CREATE INDEX IF NOT EXISTS idx_tracking_status ON tracking(parcel_status);
CREATE INDEX IF NOT EXISTS idx_tracking_delivery_date ON tracking(delivery_date);
