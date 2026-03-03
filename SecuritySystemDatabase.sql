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

-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`inside_user`
-- -----------------------------------------------------
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
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 17
DEFAULT CHARACTER SET = utf8;


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
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`Entry_logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`Entry_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `inside_user_id` INT(11) NULL,
  `outside_user_id` INT(11) NULL,
  `security_guard_user_id` INT(11) NOT NULL,
  `scan_at` VARCHAR(45) NULL,
  `scan_type` VARCHAR(50) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Entry_logs_inside_user_idx` (`inside_user_id` ASC),
  INDEX `fk_Entry_logs_outside_user_idx` (`outside_user_id` ASC),
  INDEX `fk_Entry_logs_security_guard_user1_idx` (`security_guard_user_id` ASC),
  CONSTRAINT `fk_Entry_logs_inside_user`
    FOREIGN KEY (`inside_user_id`)
    REFERENCES `securitysystemdatabase`.`inside_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Entry_logs_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Entry_logs_security_guard_user1`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

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
  `purpose_of_visit` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`visit_requests`
-- -----------------------------------------------------
-- Stores visit requests from outside users (parents/guests)
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`visit_requests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `outside_user_id` INT(11) NOT NULL,
  `visit_date` DATE NOT NULL,
  `visit_time` TIME NOT NULL,
  `purpose` TEXT NULL,
  `person_to_meet` VARCHAR(150) NULL,
  `status` VARCHAR(50) NULL DEFAULT 'pending',
  `admin_remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_visit_requests_outside_user_idx` (`outside_user_id` ASC),
  CONSTRAINT `fk_visit_requests_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`shifts`
-- -----------------------------------------------------
-- Stores scheduled shifts for security guards
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`shifts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `security_guard_user_id` INT(11) NOT NULL,
  `shift_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `status` VARCHAR(50) NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_shifts_security_guard_user_idx` (`security_guard_user_id` ASC),
  CONSTRAINT `fk_shifts_security_guard_user`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`shift_logs`
-- -----------------------------------------------------
-- Tracks actual clock in/out times and handover notes
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`shift_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `security_guard_user_id` INT(11) NOT NULL,
  `shift_id` INT NULL,
  `clock_in_time` DATETIME NULL,
  `clock_out_time` DATETIME NULL,
  `handover_note` TEXT NULL,
  `status` VARCHAR(50) NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_shift_logs_security_guard_user_idx` (`security_guard_user_id` ASC),
  INDEX `fk_shift_logs_shift_idx` (`shift_id` ASC),
  CONSTRAINT `fk_shift_logs_security_guard_user`
    FOREIGN KEY (`security_guard_user_id`)
    REFERENCES `securitysystemdatabase`.`security_guard_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_shift_logs_shift`
    FOREIGN KEY (`shift_id`)
    REFERENCES `securitysystemdatabase`.`shifts` (`id`)
    ON DELETE SET NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`notifications`
-- -----------------------------------------------------
-- Stores notifications for outside users (e.g., visit request approved/rejected)
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `outside_user_id` INT(11) NOT NULL,
  `type` VARCHAR(100) NOT NULL COMMENT 'e.g., visit_approved, visit_rejected',
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NULL DEFAULT 0,
  `related_type` VARCHAR(100) NULL COMMENT 'e.g., visit_request',
  `related_id` INT NULL COMMENT 'e.g., visit request ID',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_notifications_outside_user_idx` (`outside_user_id` ASC),
  INDEX `fk_notifications_outside_user_is_read_idx` (`outside_user_id` ASC, `is_read` ASC),
  CONSTRAINT `fk_notifications_outside_user`
    FOREIGN KEY (`outside_user_id`)
    REFERENCES `securitysystemdatabase`.`outside_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- =====================================================
-- ALTER TABLE SCRIPTS (For existing databases)
-- Run this section if you already have the database
-- =====================================================

-- Add missing columns to outside_user table (if they don't exist)
ALTER TABLE `outside_user` 
ADD COLUMN IF NOT EXISTS `fullname` VARCHAR(200) NULL AFTER `last_name`,
ADD COLUMN IF NOT EXISTS `qr_value` VARCHAR(200) NULL AFTER `updated_at`,
ADD COLUMN IF NOT EXISTS `qr_status` VARCHAR(200) NULL DEFAULT 'inactive' AFTER `qr_value`,
ADD COLUMN IF NOT EXISTS `purpose_of_visit` VARCHAR(255) NULL DEFAULT NULL AFTER `qr_status`;

-- Update existing outside_user records with fullname
SET SQL_SAFE_UPDATES = 0;
UPDATE `outside_user` 
SET `fullname` = CONCAT(`first_name`, ' ', `last_name`) 
WHERE `fullname` IS NULL OR `fullname` = '';
SET SQL_SAFE_UPDATES = 1;

-- Add outside_user_id to Entry_logs (if it doesn't exist)
ALTER TABLE `Entry_logs` 
ADD COLUMN IF NOT EXISTS `outside_user_id` INT(11) NULL AFTER `inside_user_id`;

-- Modify inside_user_id to be nullable (if not already)
ALTER TABLE `Entry_logs` 
MODIFY COLUMN `inside_user_id` INT(11) NULL;

-- Modify scan_type to be VARCHAR (if not already)
ALTER TABLE `Entry_logs` 
MODIFY COLUMN `scan_type` VARCHAR(50) NULL;
