/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_log
Target Host: localhost
Target Database: zjuhzv2_log
Date: 2008-12-5 ÏÂÎç 05:05:35
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_space
-- ----------------------------
CREATE TABLE `tb_space` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `tb_user` VALUES ('1', '1', 'add_user', '', '1226972358');
INSERT INTO `tb_user` VALUES ('2', '2', 'add_user', '', '1226972703');
