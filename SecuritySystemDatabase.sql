-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema SecuritySystemDatabase
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema SecuritySystemDatabase
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `SecuritySystemDatabase` DEFAULT CHARACTER SET utf8 ;
USE `SecuritySystemDatabase` ;

-- -----------------------------------------------------
-- Table `SecuritySystemDatabase`.`admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecuritySystemDatabase`.`admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NULL,
  `email` VARCHAR(155) NULL,
  `password` VARCHAR(100) NULL,
  `status` VARCHAR(145) NULL,
  `updated_at` VARCHAR(145) NULL,
  `remember_token` VARCHAR(145) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
