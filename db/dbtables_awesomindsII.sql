-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema awesominds
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema awesominds
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `awesominds` DEFAULT CHARACTER SET latin1 ;
USE `awesominds` ;

DROP TABLE IF EXISTS `course` ;
DROP TABLE IF EXISTS `chapter` ;
DROP TABLE IF EXISTS `invite` ;
DROP TABLE IF EXISTS `question` ;
DROP TABLE IF EXISTS `users` ;
DROP TABLE IF EXISTS `score` ;
DROP TABLE IF EXISTS `usercoursereg` ;
DROP TABLE IF EXISTS `taskattribute` ;
DROP TABLE IF EXISTS `task` ;
DROP TABLE IF EXISTS `gameattribute` ;
DROP TABLE IF EXISTS `roundlevel` ;
DROP TABLE IF EXISTS `game` ;

-- -----------------------------------------------------
-- Table `course`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `course` (
  `courseid` VARCHAR(9) NOT NULL,
  `name` TINYTEXT NOT NULL,
  `regcode` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`courseid`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `chapter`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chapter` (
  `courseid` VARCHAR(9) NOT NULL,
  `chapterid` INT(11) NOT NULL,
  `chaptername` TINYTEXT NOT NULL,
  `date_start` VARCHAR(99) NOT NULL,
  `date_end` VARCHAR(99) NOT NULL,
  PRIMARY KEY (`courseid`, `chapterid`),
  KEY `chapter_course_fk_idx` (`courseid`),
  CONSTRAINT `chapter_course_fk`
    FOREIGN KEY (`courseid`)
    REFERENCES `course` (`courseid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `invite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `invite` (
  `inviteid` INT(11) NOT NULL AUTO_INCREMENT,
  `invitecode` VARCHAR(40) NOT NULL,
  `email_sentto` VARCHAR(100) NOT NULL,
  `c_number_sentby` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`inviteid`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `question` (
  `questionid` INT(11) NOT NULL AUTO_INCREMENT,
  `question` TEXT NOT NULL,
  `chapter` INT(11) NOT NULL,
  `courseid` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`questionid`),
  KEY `question_course_fk_idx` (`courseid`),
  CONSTRAINT `question_course_fk`
    FOREIGN KEY (`courseid`)
    REFERENCES `course` (`courseid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `c_number` VARCHAR(11) NOT NULL,
  `first_name` VARCHAR(50) NULL DEFAULT NULL,
  `last_name` VARCHAR(50) NULL DEFAULT NULL,
  `play_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `hash` VARCHAR(32) NULL DEFAULT NULL,
  `avatarnum` INT(11) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT '0',
  `laston` DATETIME NULL DEFAULT NULL,
  `isInstructor` TINYINT(1) NOT NULL DEFAULT '0',
  `user_volume` DECIMAL(2,1) NOT NULL DEFAULT '0.2',
  PRIMARY KEY (`c_number`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `score`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `score` (
  `scoreid` INT(11) NOT NULL AUTO_INCREMENT,
  `chapter` INT(11) NOT NULL,
  `courseid` VARCHAR(9) NOT NULL,
  `c_number` VARCHAR(11) NOT NULL,
  `high_score` INT(11) NOT NULL,
  `total_score` INT(11) NOT NULL,
  `game_mode` INT(11) NOT NULL DEFAULT '0',
  `times_played` INT(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`scoreid`),
   KEY `user_score_fk_idx` (`c_number`),
   KEY `user_coursse_fk_idx` (`courseid`),
  CONSTRAINT `user_coursse_fk`
    FOREIGN KEY (`courseid`)
    REFERENCES `course` (`courseid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_score_fk`
    FOREIGN KEY (`c_number`)
    REFERENCES `users` (`c_number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `usercoursereg`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usercoursereg` (
  `c_number` VARCHAR(11) NOT NULL,
  `courseid` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`c_number`, `courseid`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `task`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `task` ;

CREATE TABLE IF NOT EXISTS `task` (
  `taskid` INT NOT NULL,
  `tname` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`taskid`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taskattribute`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taskattribute` ;

CREATE TABLE IF NOT EXISTS `taskattribute` (
  `taskattributeid` INT NOT NULL,
  `ptsperq` INT NOT NULL,
  `enabledstatus` TINYINT NOT NULL,
  `task_taskid` INT NOT NULL,
  `users_c_number` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`taskattributeid`),
  KEY `fk_taskattribute_task1_idx` (`task_taskid`),
  KEY `fk_taskattribute_users1_idx` (`users_c_number`),
  CONSTRAINT `fk_taskattribute_task1`
    FOREIGN KEY (`task_taskid`)
    REFERENCES `task` (`taskid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_taskattribute_users1`
    FOREIGN KEY (`users_c_number`)
    REFERENCES `users` (`c_number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game` ;

CREATE TABLE IF NOT EXISTS `game` (
  `gameid` INT NOT NULL,
  `gname` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`gameid`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gameattribute`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `gameattribute` ;

CREATE TABLE IF NOT EXISTS `gameattribute` (
  `gameattributeid` INT NOT NULL,
  `livespergame` INT NOT NULL,
  `rowbonuscount` INT NOT NULL,
  `rowbonuspts` INT NOT NULL,
  `gametheme` INT NOT NULL,
  `rndslvlspergame` INT NOT NULL,
  `users_c_number` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`gameattributeid`),
  KEY `fk_gameattribute_users1_idx` (`users_c_number`),
  CONSTRAINT `fk_gameattribute_users1`
    FOREIGN KEY (`users_c_number`)
    REFERENCES `users` (`c_number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roundlevel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `roundlevel` ;

CREATE TABLE IF NOT EXISTS `roundlevel` (
  `roundlevelid` INT NOT NULL AUTO_INCREMENT,
  `numofq` INT NOT NULL,
  `maxptsperq` INT NOT NULL,
  `goalpts` INT NOT NULL,
  `goalcompleteround` TINYINT NOT NULL,
  `goalbeatopponent` TINYINT NOT NULL,
  `game_gameid` INT NOT NULL,
  `users_c_number` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`roundlevelid`),
  KEY `fk_roundlevel_game1_idx` (`game_gameid`),
  KEY `fk_roundlevel_users1_idx` (`users_c_number`),
  CONSTRAINT `fk_roundlevel_game1`
    FOREIGN KEY (`game_gameid`)
    REFERENCES `game` (`gameid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_roundlevel_users1`
    FOREIGN KEY (`users_c_number`)
    REFERENCES `users` (`c_number`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
