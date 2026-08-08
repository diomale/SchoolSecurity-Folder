-- Applies migration schema changes that post-date the SecuritySystemDatabase.sql dump.
-- Safe to run repeatedly (idempotent). Targets the securitysystemdatabase database.

-- 2026_07_19_000001_create_admin_devices_table
CREATE TABLE IF NOT EXISTS `admin_devices` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `admin_id` bigint unsigned NOT NULL,
    `device_fingerprint` varchar(255) NOT NULL,
    `ip_address` varchar(255) DEFAULT NULL,
    `user_agent` text,
    `browser` varchar(255) DEFAULT NULL,
    `os` varchar(255) DEFAULT NULL,
    `is_trusted` tinyint(1) NOT NULL DEFAULT '0',
    `last_used_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_devices_admin_id_index` (`admin_id`),
    KEY `admin_devices_device_fingerprint_index` (`device_fingerprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2026_07_18_233403_create_admin_activity_logs_table
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `admin_id` bigint unsigned NOT NULL,
    `admin_name` varchar(200) NOT NULL,
    `category` varchar(50) NOT NULL,
    `action` varchar(100) NOT NULL,
    `description` text NOT NULL,
    `metadata` json DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` varchar(500) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_activity_logs_category_index` (`category`),
    KEY `admin_activity_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2026_07_18_231439_add_can_create_events_to_inside_user_table
ALTER TABLE `inside_user`
    ADD COLUMN IF NOT EXISTS `can_create_events` tinyint(1) NOT NULL DEFAULT '0' AFTER `qr_status`;

-- 2026_07_18_235117_add_event_end_date_to_events_table
ALTER TABLE `events`
    ADD COLUMN IF NOT EXISTS `event_end_date` date DEFAULT NULL AFTER `event_date`;

-- 2026_07_19_011300_add_email_verification_to_outside_user_table
ALTER TABLE `outside_user`
    ADD COLUMN IF NOT EXISTS `email_verified_at` timestamp NULL DEFAULT NULL AFTER `status`,
    ADD COLUMN IF NOT EXISTS `email_verification_token` varchar(100) DEFAULT NULL AFTER `email_verified_at`;

-- 2026_07_19_180335_add_terms_accepted_at_to_users_tables
ALTER TABLE `outside_user`
    ADD COLUMN IF NOT EXISTS `terms_accepted_at` timestamp NULL DEFAULT NULL AFTER `email_verified_at`;

ALTER TABLE `inside_user`
    ADD COLUMN IF NOT EXISTS `terms_accepted_at` timestamp NULL DEFAULT NULL;
