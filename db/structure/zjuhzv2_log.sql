/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_log
Target Host: localhost
Target Database: zjuhzv2_log
Date: 2009/1/22 15:49:21
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_space_bar
-- ----------------------------
CREATE TABLE `tb_space_bar` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL default '0',
  `type` char(25) NOT NULL,
  `value` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_space_group
-- ----------------------------
CREATE TABLE `tb_space_group` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL default '0',
  `type` char(25) NOT NULL,
  `value` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_user
-- ----------------------------
CREATE TABLE `tb_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `type` char(25) NOT NULL,
  `value` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
