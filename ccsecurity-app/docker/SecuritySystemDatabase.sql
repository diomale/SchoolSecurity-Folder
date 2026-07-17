-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `securitysystemdatabase` DEFAULT CHARACTER SET utf8 ;
-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `securitysystemdatabase` DEFAULT CHARACTER SET utf8 ;
USE `securitysystemdatabase` ;
USE `securitysystemdatabase` ;

-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(155) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(145) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`cleanup_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`cleanup_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `auto_delete_enabled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=enabled, 0=disabled',
  `retention_days` INT(11) NOT NULL DEFAULT 30 COMMENT 'Number of days to keep records',
  `last_cleanup_date` TIMESTAMP NULL DEFAULT NULL COMMENT 'Last time cleanup ran',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores auto-delete cleanup settings for admin control';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`cleanup_table_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`cleanup_table_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(50) NOT NULL COMMENT 'entry_logs, visit_requests, notifications, shift_logs',
  `auto_delete_enabled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=enabled, 0=disabled',
  `retention_days` INT(11) NOT NULL DEFAULT 30 COMMENT 'Number of days to keep records',
  `last_cleanup_date` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_table_name` (`table_name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8
COMMENT = 'Individual cleanup settings for each table';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`inside_user`
-- -----------------------------------------------------
-- OPTIMIZED for 2000+ students - added QR and status indexes
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`inside_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(200) NULL DEFAULT NULL,
  `fullname` VARCHAR(200) NULL DEFAULT NULL,
  `first_name` VARCHAR(150) NULL DEFAULT NULL,
  `last_name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(150) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `qr_value` VARCHAR(200) NULL DEFAULT NULL,
  `qr_status` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  -- PERFORMANCE INDEXES (CRITICAL for 2000+ students)
  INDEX `idx_inside_user_qr` (`qr_value` ASC) COMMENT 'Fast QR lookup during scanning',
  INDEX `idx_inside_user_status` (`status` ASC) COMMENT 'Filter by active/inactive',
  INDEX `idx_inside_user_email` (`email` ASC) COMMENT 'Login lookup'
)
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8
COMMENT = 'Inside users (students/staff) - optimized with indexes';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`security_guard_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`security_guard_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(200) NULL DEFAULT NULL,
  `first_name` VARCHAR(150) NULL DEFAULT NULL,
  `last_name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(150) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `status` VARCHAR(45) NULL DEFAULT NULL,
  `profile_picture` BLOB NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`entry_logs`
-- -----------------------------------------------------
-- OPTIMIZED for 2000+ students (4000+ daily scans)
-- Changes: scan_at TIMESTAMP (was VARCHAR), added critical indexes
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`entry_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `inside_user_id` INT(11) NULL DEFAULT NULL,
  `outside_user_id` INT(11) NULL DEFAULT NULL,
  `quick_pass_id` INT(11) NULL DEFAULT NULL,
  `event_registration_id` INT(11) NULL DEFAULT NULL,
  `qr_value` VARCHAR(100) NULL DEFAULT NULL,
  `security_guard_user_id` INT(11) NOT NULL,
  `scan_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'QR scan timestamp - indexed for performance',
  `scan_type` VARCHAR(50) NULL DEFAULT NULL COMMENT 'entry or exit',
  PRIMARY KEY (`id`),
  -- Foreign key indexes (existing)
  INDEX `fk_Entry_logs_inside_user_idx` (`inside_user_id` ASC) ,
  INDEX `fk_Entry_logs_security_guard_user1_idx` (`security_guard_user_id` ASC) ,
  INDEX `fk_Entry_logs_quick_passes_idx` (`quick_pass_id` ASC) ,
  INDEX `fk_Entry_logs_event_registrations_idx` (`event_registration_id` ASC) ,
  -- PERFORMANCE INDEXES for 2000+ students (CRITICAL)
  INDEX `idx_scan_at` (`scan_at` ASC) COMMENT 'Fast date range queries',
  INDEX `idx_qr_value` (`qr_value` ASC) COMMENT 'Fast QR lookup during scanning',
  INDEX `idx_scan_type` (`scan_type` ASC) COMMENT 'Filter entry vs exit',
  INDEX `idx_scan_at_type` (`scan_at` ASC, `scan_type` ASC) COMMENT 'Composite for dashboard stats',
  INDEX `idx_inside_user_id_lookup` (`inside_user_id` ASC) COMMENT 'User entry history',
  INDEX `idx_outside_user_id_lookup` (`outside_user_id` ASC) COMMENT 'Visitor history',
  CONSTRAINT `fk_Entry_logs_inside_user`
    FOREIGN KEY (`inside_user_id`)
    REFERENCES `securitysystemdatabase`.`inside_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Entry_logs_security_guard_user1`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Entry_logs_quick_passes`
    FOREIGN KEY (`quick_pass_id`)
    REFERENCES `securitysystemdatabase`.`quick_passes` (`id`)
    ON DELETE SET NULL,
  CONSTRAINT `fk_Entry_logs_event_registrations`
    FOREIGN KEY (`event_registration_id`)
    REFERENCES `securitysystemdatabase`.`event_registrations` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
AUTO_INCREMENT = 206
DEFAULT CHARACTER SET = utf8
COMMENT = 'QR scan logs - optimized with indexes for high-volume scanning (2000+ students)';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`migrations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`outside_user`
-- -----------------------------------------------------
-- OPTIMIZED for 2000+ students - added QR and status indexes
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`outside_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NULL DEFAULT NULL,
  `last_name` VARCHAR(45) NULL DEFAULT NULL,
  `fullname` VARCHAR(200) NULL DEFAULT NULL,
  `email` VARCHAR(45) NULL DEFAULT NULL,
  `phone_number` VARCHAR(45) NULL DEFAULT NULL,
  `password` VARCHAR(255) NULL DEFAULT NULL,
  `profile_picture` VARCHAR(45) NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `qr_value` VARCHAR(200) NULL DEFAULT NULL,
  `qr_status` VARCHAR(200) NULL DEFAULT 'inactive',
  `qr_expires_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'QR code expiration timestamp',
  `purpose_of_visit` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  -- PERFORMANCE INDEXES (CRITICAL for 2000+ students)
  INDEX `idx_outside_user_qr` (`qr_value` ASC) COMMENT 'Fast QR lookup during scanning',
  INDEX `idx_outside_user_status` (`status` ASC) COMMENT 'Filter by approval status',
  INDEX `idx_outside_user_email` (`email` ASC) COMMENT 'Login lookup'
)
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8
COMMENT = 'Outside users (visitors) - optimized with indexes';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`notifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `outside_user_id` INT(11) NOT NULL,
  `type` VARCHAR(100) NOT NULL COMMENT 'e.g., visit_approved, visit_rejected',
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NULL DEFAULT 0,
  `related_type` VARCHAR(100) NULL DEFAULT NULL COMMENT 'e.g., visit_request',
  `related_id` INT(11) NULL DEFAULT NULL COMMENT 'e.g., visit request ID',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_notifications_outside_user_idx` (`outside_user_id` ASC) ,
  INDEX `fk_notifications_outside_user_is_read_idx` (`outside_user_id` ASC, `is_read` ASC) ,
  CONSTRAINT `fk_notifications_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`shifts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`shifts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `security_guard_user_id` INT(11) NOT NULL,
  `shift_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `status` VARCHAR(50) NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_shifts_security_guard_user_idx` (`security_guard_user_id` ASC) ,
  CONSTRAINT `fk_shifts_security_guard_user`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 71
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`shift_logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`shift_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `security_guard_user_id` INT(11) NOT NULL,
  `shift_id` INT(11) NULL DEFAULT NULL,
  `clock_in_time` DATETIME NULL DEFAULT NULL,
  `clock_out_time` DATETIME NULL DEFAULT NULL,
  `handover_note` TEXT NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_shift_logs_security_guard_user_idx` (`security_guard_user_id` ASC) ,
  INDEX `fk_shift_logs_shift_idx` (`shift_id` ASC) ,
  CONSTRAINT `fk_shift_logs_security_guard_user`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_shift_logs_shift`
    FOREIGN KEY (`shift_id`)
    REFERENCES `securitysystemdatabase`.`shifts` (`id`)
    ON DELETE SET NULL)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`visit_requests`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`visit_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `outside_user_id` INT(11) NOT NULL,
  `visit_date` DATE NOT NULL,
  `visit_time` TIME NOT NULL,
  `purpose` TEXT NULL DEFAULT NULL,
  `person_to_meet` VARCHAR(150) NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT 'pending',
  `admin_remarks` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_visit_requests_outside_user_idx` (`outside_user_id` ASC) ,
  CONSTRAINT `fk_visit_requests_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`quick_passes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`quick_passes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visitor_name` VARCHAR(150) NOT NULL,
  `vehicle_plate` VARCHAR(20) NULL DEFAULT NULL,
  `purpose` VARCHAR(50) NOT NULL DEFAULT 'Other',
  `qr_value` VARCHAR(100) NOT NULL UNIQUE,
  `valid_date` DATE NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `status` ENUM('active', 'used', 'expired') NOT NULL DEFAULT 'active',
  `created_by_guard_id` INT(11) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_qr_value` (`qr_value` ASC),
  INDEX `idx_valid_date` (`valid_date` ASC),
  INDEX `idx_status` (`status` ASC),
  INDEX `fk_quick_passes_guard_idx` (`created_by_guard_id` ASC),
  CONSTRAINT `fk_quick_passes_guard`
    FOREIGN KEY (`created_by_guard_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Temporary same-day visitor passes';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`parent_child_connections`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`parent_child_connections` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `outside_user_id` INT(11) NOT NULL COMMENT 'Parent/Visitor ID',
  `inside_user_id` INT(11) NOT NULL COMMENT 'Child/Student ID',
  `relationship` VARCHAR(100) NOT NULL COMMENT 'e.g., Father, Mother, Guardian',
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `inside_user_approval` VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, accepted, rejected by inside user',
  `admin_remarks` TEXT NULL DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_parent_child_outside_user_idx` (`outside_user_id` ASC),
  INDEX `fk_parent_child_inside_user_idx` (`inside_user_id` ASC),
  INDEX `fk_parent_child_status_idx` (`status` ASC),
  UNIQUE INDEX `unique_parent_child_pair` (`outside_user_id` ASC, `inside_user_id` ASC),
  CONSTRAINT `fk_parent_child_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_parent_child_inside_user`
    FOREIGN KEY (`inside_user_id`)
    REFERENCES `securitysystemdatabase`.`inside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores parent-child connection requests for tracking student entry/exit notifications';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `inside_user_id` INT(11) NOT NULL,
  `event_name` VARCHAR(255) NOT NULL,
  `event_description` TEXT NULL DEFAULT NULL,
  `event_date` DATE NOT NULL,
  `event_start_time` TIME NOT NULL,
  `event_end_time` TIME NOT NULL,
  `qr_request_deadline` DATETIME NOT NULL COMMENT 'Registration deadline - uses DATETIME to preserve exact value',
  `alien_user_limit` INT(11) NOT NULL DEFAULT 50,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected, cancelled, completed',
  `admin_remarks` TEXT NULL DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `show_on_welcome` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=show event on welcome page, 0=hide',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  INDEX `fk_events_inside_user_idx` (`inside_user_id` ASC),
  INDEX `idx_events_status` (`status` ASC),
  INDEX `idx_events_date` (`event_date` ASC),
  INDEX `idx_events_show_welcome` (`show_on_welcome` ASC),
  CONSTRAINT `fk_events_inside_user`
    FOREIGN KEY (`inside_user_id`)
    REFERENCES `securitysystemdatabase`.`inside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores events created by inside users for alien user registration';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`event_registrations`
-- -----------------------------------------------------
-- OPTIMIZED for 2000+ students - QR code index already present
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`event_registrations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event_id` INT(11) NOT NULL,
  `outside_user_id` INT(11) NULL DEFAULT NULL,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(50) NULL DEFAULT NULL,
  `qr_code` VARCHAR(100) NOT NULL UNIQUE,
  `qr_downloaded` TINYINT(1) NOT NULL DEFAULT 0,
  `qr_downloaded_at` TIMESTAMP NULL DEFAULT NULL,
  `qr_emailed` TINYINT(1) NOT NULL DEFAULT 0,
  `qr_emailed_at` TIMESTAMP NULL DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'registered' COMMENT 'registered, checked_in, checked_out',
  `checked_in_at` TIMESTAMP NULL DEFAULT NULL,
  `checked_out_at` TIMESTAMP NULL DEFAULT NULL,
  `needs_creator_approval` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=requires creator approval before QR is sent',
  `creator_approved_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp when event creator approved the registration',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  -- Foreign key indexes
  INDEX `fk_event_registrations_events_idx` (`event_id` ASC),
  INDEX `fk_event_registrations_outside_user_idx` (`outside_user_id` ASC),
  -- PERFORMANCE INDEXES (CRITICAL for 2000+ students)
  INDEX `idx_registrations_status` (`status` ASC) COMMENT 'Filter by registration status',
  INDEX `idx_registrations_qr_code` (`qr_code` ASC) COMMENT 'Fast QR lookup during event check-in',
  INDEX `idx_registrations_needs_approval` (`needs_creator_approval` ASC) COMMENT 'Pending approvals filter',
  CONSTRAINT `fk_event_registrations_events`
    FOREIGN KEY (`event_id`)
    REFERENCES `securitysystemdatabase`.`events` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_registrations_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores event registrations for alien users with QR codes and creator approval workflow';


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`currently_inside`
-- -----------------------------------------------------
-- OPTIMIZED for real-time tracking of people currently inside campus
-- Prevents memory crash when querying 1M+ entry logs
-- Updated on each QR scan (entry = insert, exit = delete)
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`currently_inside` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `qr_value` VARCHAR(50) NOT NULL COMMENT 'QR code value for quick lookup',
  `user_type` ENUM('inside', 'outside', 'event', 'quick') NOT NULL COMMENT 'Type of user',
  `user_id` INT(11) NOT NULL COMMENT 'ID of the user (inside_user_id, outside_user_id, etc)',
  `fullname` VARCHAR(200) NOT NULL COMMENT 'Full name for display',
  `email` VARCHAR(150) NOT NULL COMMENT 'Email for contact',
  `role` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Role: student, staff, visitor, etc',
  `entered_at` TIMESTAMP NOT NULL COMMENT 'Entry time',
  `entry_log_id` INT(11) NOT NULL COMMENT 'Reference to entry_logs table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_qr` (`qr_value`) COMMENT 'One record per QR code',
  INDEX `idx_entered_at` (`entered_at`) COMMENT 'Sort by entry time',
  INDEX `idx_user_type` (`user_type`) COMMENT 'Filter by user type'
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Real-time tracking of people currently inside campus - prevents memory crash with 1M+ records';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
