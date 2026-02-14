-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema securitysystemdatabase
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `securitysystemdatabase` DEFAULT CHARACTER SET utf8mb3 ;
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
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `securitysystemdatabase`.`inside_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `securitysystemdatabase`.`inside_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(200) NULL DEFAULT NULL,
  `fullname` VARCHAR(200) NULL DEFAULT NULL,
  `first_name` VARCHAR(150) NULL DEFAULT NULL,
  `last_name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(150) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
