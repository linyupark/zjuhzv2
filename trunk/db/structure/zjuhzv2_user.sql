/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_user
Target Host: localhost
Target Database: zjuhzv2_user
Date: 2008-10-17 ÏÂÎç 03:48:23
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_base
-- ----------------------------
CREATE TABLE `tb_base` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `account` char(16) NOT NULL,
  `password` char(32) NOT NULL,
  `username` char(12) NOT NULL,
  `sex` char(3) NOT NULL,
  `role` char(9) NOT NULL,
  `regtime` int(10) unsigned NOT NULL,
  `point` int(10) unsigned NOT NULL default '0',
  `lastlogin` int(10) unsigned default NULL,
  `birthday` int(10) unsigned default NULL,
  `hometown` char(30) default NULL,
  `city` char(30) default NULL,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `ix_account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_career
-- ----------------------------
CREATE TABLE `tb_career` (
  `uid` int(10) unsigned NOT NULL,
  `company` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `job` varchar(255) NOT NULL,
  `start` int(10) unsigned default NULL,
  `end` int(10) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_contact
-- ----------------------------
CREATE TABLE `tb_contact` (
  `uid` int(10) unsigned NOT NULL,
  `email` char(40) NOT NULL,
  `qq` char(15) default NULL,
  `msn` char(40) default NULL,
  `address` varchar(255) default NULL,
  `zipcode` char(6) default NULL,
  `tel` char(15) default NULL,
  `mobile` char(11) default NULL,
  UNIQUE KEY `ix_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_ctrl
-- ----------------------------
CREATE TABLE `tb_ctrl` (
  `uid` int(10) unsigned NOT NULL,
  `private` text NOT NULL,
  `home` text NOT NULL,
  UNIQUE KEY `ix_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_edu
-- ----------------------------
CREATE TABLE `tb_edu` (
  `uid` int(10) unsigned NOT NULL,
  `campus` varchar(255) NOT NULL,
  `year` char(4) NOT NULL,
  `class` varchar(255) NOT NULL,
  UNIQUE KEY `ix_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_intro
-- ----------------------------
CREATE TABLE `tb_intro` (
  `uid` int(11) unsigned NOT NULL,
  `intro` varchar(255) default NULL,
  `motto` varchar(255) default NULL,
  `wish` varchar(255) default NULL,
  `interest` varchar(255) default NULL,
  `book` varchar(255) default NULL,
  `movie` varchar(255) default NULL,
  `idol` varchar(255) default NULL,
  `tv` varchar(255) default NULL,
  UNIQUE KEY `ix_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
