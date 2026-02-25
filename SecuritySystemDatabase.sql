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
  `inside_user_id` INT(11) NOT NULL,
  `security_guard_user_id` INT(11) NOT NULL,
  `scan_at` VARCHAR(45) NULL,
  `scan_type` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_Entry_logs_inside_user_idx` (`inside_user_id` ASC),
  INDEX `fk_Entry_logs_security_guard_user1_idx` (`security_guard_user_id` ASC),
  CONSTRAINT `fk_Entry_logs_inside_user`
    FOREIGN KEY (`inside_user_id`)
    REFERENCES `securitysystemdatabase`.`inside_user` (`id`)
    ON DELETE NO ACTION
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
  `email` VARCHAR(45) NULL DEFAULT NULL,
  `phone_number` VARCHAR(45) NULL DEFAULT NULL,
  `password` VARCHAR(255) NULL DEFAULT NULL,
  `profile_picture` VARCHAR(45) NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
