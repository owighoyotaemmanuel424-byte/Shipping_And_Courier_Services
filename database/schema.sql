-- NEXTGEN Logistics MySQL schema
-- Compatible with database/config.php and the existing admin/tracking PHP handlers.
-- Back up any existing database before importing this file into a populated database.

CREATE TABLE IF NOT EXISTS admin (
    adminid INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(190) NOT NULL,
    passw VARCHAR(255) NOT NULL,
    PRIMARY KEY (adminid),
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tracking (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(190) NOT NULL,
    last_name VARCHAR(190) NOT NULL,
    tracking_number VARCHAR(100) NOT NULL,
    shipment_type VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    shipment_date DATE NOT NULL,
    delivery_date DATE NOT NULL,
    source_city VARCHAR(190) NOT NULL,
    source_state VARCHAR(190) NOT NULL,
    source_zip VARCHAR(40) NOT NULL DEFAULT '',
    current_city VARCHAR(190) NOT NULL,
    current_state VARCHAR(190) NOT NULL,
    current_zip VARCHAR(40) NOT NULL DEFAULT '',
    destination_city VARCHAR(190) NOT NULL,
    destination_state VARCHAR(190) NOT NULL,
    destination_zip VARCHAR(40) NOT NULL DEFAULT '',
    contact_number VARCHAR(60) NOT NULL,
    parcel_status VARCHAR(100) NOT NULL DEFAULT 'Packaging',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tracking_number (tracking_number),
    KEY idx_tracking_status (parcel_status),
    KEY idx_tracking_delivery_date (delivery_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
