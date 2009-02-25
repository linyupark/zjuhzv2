/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_log
Target Host: localhost
Target Database: zjuhzv2_log
Date: 2009-2-25 ÏÂÎç 05:10:05
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_event
-- ----------------------------
CREATE TABLE `tb_event` (
  `lid` bigint(20) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(11) NOT NULL default '0',
  `tid` bigint(20) NOT NULL default '0',
  `gid` int(10) unsigned NOT NULL default '0',
  `key` char(30) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`lid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
