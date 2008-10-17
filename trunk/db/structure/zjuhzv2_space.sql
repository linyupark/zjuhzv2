/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_space
Target Host: localhost
Target Database: zjuhzv2_space
Date: 2008-10-17 ÏÂÎç 03:48:17
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_events
-- ----------------------------
CREATE TABLE `tb_events` (
  `tid` int(10) unsigned NOT NULL,
  `sign` int(10) unsigned NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `member` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_friends
-- ----------------------------
CREATE TABLE `tb_friends` (
  `uid` int(10) unsigned NOT NULL,
  `friend` int(10) unsigned NOT NULL,
  `sort` int(10) unsigned NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_friends_sort
-- ----------------------------
CREATE TABLE `tb_friends_sort` (
  `uid` int(10) unsigned NOT NULL,
  `sorts` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_group
-- ----------------------------
CREATE TABLE `tb_group` (
  `gid` int(10) unsigned NOT NULL auto_increment,
  `name` char(30) NOT NULL,
  `member` text NOT NULL,
  `creater` int(10) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `visit` int(10) unsigned NOT NULL,
  `intro` varchar(255) default NULL,
  `point` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_home
-- ----------------------------
CREATE TABLE `tb_home` (
  `uid` int(10) unsigned NOT NULL,
  `ing` varchar(255) default NULL,
  `guests` text,
  `groups` text,
  `favor` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_msg
-- ----------------------------
CREATE TABLE `tb_msg` (
  `mid` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default '0' COMMENT '0:pm;1:system;2:guestbook',
  `parent` bigint(20) unsigned default NULL,
  `content` text NOT NULL,
  `sender` int(10) unsigned NOT NULL,
  `incept` int(11) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `isread` tinyint(1) unsigned NOT NULL default '0',
  `sendbox` tinyint(1) unsigned NOT NULL default '1',
  `inceptbox` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_news
-- ----------------------------
CREATE TABLE `tb_news` (
  `tid` int(10) unsigned NOT NULL,
  `sort` int(10) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  `modtime` int(10) unsigned default NULL,
  `private` tinyint(1) unsigned NOT NULL default '0' COMMENT '0:self;1:member;2:group;3:friends;4:all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_news_sort
-- ----------------------------
CREATE TABLE `tb_news_sort` (
  `sort` int(10) unsigned NOT NULL auto_increment,
  `name` char(30) NOT NULL,
  `rate` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_photo
-- ----------------------------
CREATE TABLE `tb_photo` (
  `tid` int(10) unsigned NOT NULL,
  `file` varchar(255) NOT NULL,
  `description` tinytext,
  `private` tinyint(1) unsigned NOT NULL default '0' COMMENT '0:self;1:member;2:group;3:friends;4:all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_reply
-- ----------------------------
CREATE TABLE `tb_reply` (
  `rid` int(10) unsigned NOT NULL auto_increment,
  `tid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `whisper` tinyint(1) unsigned NOT NULL default '0',
  `deny` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_tbar
-- ----------------------------
CREATE TABLE `tb_tbar` (
  `tid` int(10) unsigned NOT NULL auto_increment,
  `type` char(5) NOT NULL,
  `title` char(90) NOT NULL,
  `puber` int(10) unsigned NOT NULL,
  `pubtime` int(10) unsigned NOT NULL,
  `click` int(10) unsigned NOT NULL default '0',
  `reply` int(10) unsigned NOT NULL default '0',
  `replyer` int(10) unsigned default NULL,
  `replytime` int(10) unsigned default NULL,
  `rate` int(10) unsigned NOT NULL default '0',
  `group` int(10) unsigned NOT NULL default '0',
  `freeze` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_topic
-- ----------------------------
CREATE TABLE `tb_topic` (
  `tid` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `modtime` int(10) unsigned default NULL,
  `private` tinyint(1) unsigned NOT NULL default '0' COMMENT '0:self;1:member;2:group;3:friends;4:all'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
