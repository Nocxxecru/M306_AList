-- MySQL Script generated by MySQL Workbench
-- Wed Mar 11 08:50:33 2020
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema dbalist
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema dbalist
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `dbalist` DEFAULT CHARACTER SET utf8 ;
USE `dbalist` ;

-- -----------------------------------------------------
-- Table `dbalist`.`t_anime`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbalist`.`t_anime` (
  `idAnime` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) ,
  `avgNote` DOUBLE NULL DEFAULT NULL,
  `addDate` DATETIME NOT NULL,
  `cover` MEDIUMBLOB NOT NULL,
  `description` VARCHAR(250),
  PRIMARY KEY (`idAnime`),
  UNIQUE INDEX `idAnime_UNIQUE` (`idAnime` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `dbalist`.`t_role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbalist`.`t_role` (
  `idRole` INT(11) NOT NULL,
  `roleName` VARCHAR(20) ,
  PRIMARY KEY (`idRole`),
  UNIQUE INDEX `idRole_UNIQUE` (`idRole` ASC) ,
  UNIQUE INDEX `roleName_UNIQUE` (`roleName` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `dbalist`.`t_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbalist`.`t_user` (
  `idUser` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) ,
  `password` VARCHAR(80) ,
  `email` VARCHAR(25) ,
  `logo` VARCHAR(25) NOT NULL,
  `email_token` VARCHAR(100) ,
  `activated` INT(1) NOT NULL,
  `idRole` INT(11) NOT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE INDEX `idUser_UNIQUE` (`idUser` ASC) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  UNIQUE INDEX `email_token_UNIQUE` (`email_token` ASC) ,
  INDEX `fk_t_User_t_Role1_idx` (`idRole` ASC) ,
  CONSTRAINT `fk_t_User_t_Role1`
    FOREIGN KEY (`idRole`)
    REFERENCES `dbalist`.`t_role` (`idRole`))
ENGINE = InnoDB
AUTO_INCREMENT = 39
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `dbalist`.`t_friends`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbalist`.`t_friends` (
  `idUser1` INT(11) NOT NULL,
  `idUser2` INT(11) NOT NULL,
  PRIMARY KEY (`idUser1`, `idUser2`),
  INDEX `fk_t_User_has_t_User_t_User1_idx` (`idUser2` ASC) ,
  INDEX `fk_t_User_has_t_User_t_User_idx` (`idUser1` ASC) ,
  CONSTRAINT `fk_t_User_has_t_User_t_User`
    FOREIGN KEY (`idUser1`)
    REFERENCES `dbalist`.`t_user` (`idUser`),
  CONSTRAINT `fk_t_User_has_t_User_t_User1`
    FOREIGN KEY (`idUser2`)
    REFERENCES `dbalist`.`t_user` (`idUser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `dbalist`.`t_library`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbalist`.`t_library` (
  `idUser` INT(11) NOT NULL,
  `idAnime` INT(11) NOT NULL,
  `note` DOUBLE NOT NULL,
  `dateWatched` DATETIME NULL,
  PRIMARY KEY (`idUser`, `idAnime`),
  INDEX `fk_t_User_has_t_Anime_t_Anime1_idx` (`idAnime` ASC) ,
  INDEX `fk_t_User_has_t_Anime_t_User1_idx` (`idUser` ASC) ,
  CONSTRAINT `fk_t_User_has_t_Anime_t_Anime1`
    FOREIGN KEY (`idAnime`)
    REFERENCES `dbalist`.`t_anime` (`idAnime`),
  CONSTRAINT `fk_t_User_has_t_Anime_t_User1`
    FOREIGN KEY (`idUser`)
    REFERENCES `dbalist`.`t_user` (`idUser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
