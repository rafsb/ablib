-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: localdb
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB
-- Host: 127.0.0.1    Database: localdb
-- ------------------------------------------------------
--

-- Pré process directives
-- ------------------------------------------------------
--

drop database if exists localdb;
create database if not exists localdb;
use localdb;

-- set global sql_mode=(select replace(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
-- create user if not exists abox_dba@localhost identified by 'rootz';
-- grant all privileges on sharestatedb.* to abox_dba@localhost;
-- flush privileges;

alter schema localdb default character set utf8 default collate utf8_general_ci;
--
-- Table structure for table `Users`
--
DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
    `code` CHAR(32) NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `mail` VARCHAR(64) DEFAULT NULL,
    `cel0` CHAR(15) DEFAULT NULL,
    `stts` INT(1) NOT NULL DEFAULT '0',
    `alvl` INT(1) NOT NULL DEFAULT '0',
    `cdat` CHAR(19) DEFAULT NULL,
    `last` CHAR(19) DEFAULT NULL,
    `view` INT(11) NOT NULL DEFAULT '0',
    `mchk` TINYINT(1) NOT NULL DEFAULT '0',
    `user` VARCHAR(16) NOT NULL DEFAULT 'user',
    `pswd` CHAR(64) NOT NULL DEFAULT '0',
    `home` VARCHAR(32) DEFAULT NULL,
    PRIMARY KEY (`code`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;
--
-- Dumping data for table `Users`
--
INSERT INTO `Users` VALUES (
	'aboxaboxaboxaboxaboxaboxaboxabox',
    'Aboxsoft',
    'contato@aboxsoft.me',
    '(12)98176-0703',
    1,
    9,
    '12/02/1988_08:58:32',
    '06/09/2017_14:58:38',
    0,
    1,
    'abox',
    'd6690c910e4151608b023d400858c3923f268ced3370745e9df65560ca1a5b49',    
    'home'
);