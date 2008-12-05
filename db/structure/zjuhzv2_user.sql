/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_user
Target Host: localhost
Target Database: zjuhzv2_user
Date: 2008-12-5 ���� 05:05:21
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
  `nickname` char(30) default NULL,
  `lastlogin` int(10) unsigned default NULL,
  `birthyear` char(5) default NULL,
  `birthmonth` char(2) default NULL,
  `birthday` char(2) default NULL,
  `hometown` char(30) default NULL,
  `city` char(30) default NULL,
  `marriage` tinyint(2) unsigned default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `ix_account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_career
-- ----------------------------
CREATE TABLE `tb_career` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `company` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `job` varchar(255) NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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
-- Table structure for tb_edu
-- ----------------------------
CREATE TABLE `tb_edu` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `campus` varchar(255) NOT NULL,
  `year` char(4) NOT NULL,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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
-- Table structure for tb_privacy
-- ----------------------------
CREATE TABLE `tb_privacy` (
  `uid` int(10) unsigned NOT NULL,
  `access` text NOT NULL,
  `home` text NOT NULL,
  UNIQUE KEY `ix_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `tb_base` VALUES ('1', 'linyupark', '4297f44b13955235245b2497399d7a93', '林宇', '男', 'member', '1226972358', 'linyupark', '1228455281', '80后', '2', '14', '温州', '杭州', '0');
INSERT INTO `tb_base` VALUES ('2', 'fangzixin', '4297f44b13955235245b2497399d7a93', '方梓欣', '男', 'bench', '1226972703', null, null, null, null, null, null, null, null);
INSERT INTO `tb_career` VALUES ('3', '1', '杭州友笑网络科技有限公司', '技术部', 'WEB工程师', '1206489600', '0');
INSERT INTO `tb_career` VALUES ('4', '1', '杭州巴达星网络技术有限公司', '技术部', '网页设计师', '1148688000', '1193443200');
INSERT INTO `tb_contact` VALUES ('1', 'linyupark@gmail.com', '178673235', 'linyupark@hotmail.com', '杭州九莲新村35幢1单元302室', '310012', '057188087620', '13588063020');
INSERT INTO `tb_contact` VALUES ('2', 'fangzixin@gmail.com', null, null, null, null, null, null);
INSERT INTO `tb_edu` VALUES ('4', '1', '理学院', '2006', '06工商管理');
INSERT INTO `tb_intro` VALUES ('1', '', '', '', '篮球', '', '英雄本色', 'MJ', '越狱');
INSERT INTO `tb_privacy` VALUES ('1', 'a:12:{s:5:\"vhome\";s:1:\"1\";s:8:\"username\";s:1:\"2\";s:8:\"nickname\";s:1:\"2\";s:3:\"sex\";s:1:\"2\";s:5:\"birth\";s:1:\"2\";s:8:\"hometown\";s:1:\"2\";s:4:\"city\";s:1:\"2\";s:8:\"marriage\";s:1:\"1\";s:3:\"edu\";s:1:\"2\";s:5:\"intro\";s:1:\"1\";s:7:\"contact\";s:1:\"1\";s:6:\"career\";s:1:\"1\";}', 'a:11:{i:0;s:8:\"username\";i:1;s:8:\"nickname\";i:2;s:3:\"sex\";i:3;s:5:\"birth\";i:4;s:8:\"hometown\";i:5;s:4:\"city\";i:6;s:8:\"marriage\";i:7;s:3:\"edu\";i:8;s:5:\"intro\";i:9;s:7:\"contact\";i:10;s:6:\"career\";}');
INSERT INTO `tb_privacy` VALUES ('123', 'a:12:{s:5:\"vhome\";s:1:\"1\";s:8:\"username\";s:1:\"2\";s:3:\"sex\";s:1:\"2\";s:8:\"nickname\";s:1:\"2\";s:5:\"birth\";s:1:\"1\";s:8:\"hometown\";s:1:\"2\";s:4:\"city\";s:1:\"2\";s:8:\"marriage\";s:1:\"1\";s:5:\"intro\";s:1:\"2\";s:7:\"contact\";s:1:\"1\";s:3:\"edu\";s:1:\"2\";s:6:\"career\";s:1:\"1\";}', 'a:3:{i:0;s:8:\"username\";i:1;s:5:\"intro\";i:2;s:3:\"edu\";}');
