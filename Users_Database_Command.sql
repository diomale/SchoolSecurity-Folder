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
CREATE SCHEMA IF NOT EXISTS `securitysystemdatabase` DEFAULT CHARACTER SET utf8mb3 ;
USE `securitysystemdatabase` ;

-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`inside_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`inside_user` (
  `id` INT NOT NULL,
  `role` VARCHAR(200) NULL,
  `fullname` VARCHAR(200) NULL,
  `first_name` VARCHAR(150) NULL,
  `last_name` VARCHAR(150) NULL,
  `email` VARCHAR(150) NULL,
  `password` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

USE `securitysystemdatabase` ;

-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(155) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(145) NULL DEFAULT NULL,
  `updated_at` VARCHAR(145) NULL DEFAULT NULL,
  `remember_token` VARCHAR(145) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
