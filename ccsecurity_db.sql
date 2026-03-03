-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema ccsecurity-db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ccsecurity-db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ccsecurity-db` DEFAULT CHARACTER SET utf8mb3 ;
-- -----------------------------------------------------
-- Schema ccsecurity-db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ccsecurity-db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ccsecurity-db` DEFAULT CHARACTER SET utf8mb3 ;
USE `ccsecurity-db` ;
USE `ccsecurity-db` ;

-- -----------------------------------------------------
-- Table `ccsecurity-db`.`super_admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ccsecurity-db`.`super_admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(150) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(145) NULL DEFAULT NULL,
  `remember_token` VARCHAR(145) NULL DEFAULT NULL,
  `updated_at` VARCHAR(145) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
