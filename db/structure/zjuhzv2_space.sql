/*
MySQL Data Transfer
Source Host: localhost
Source Database: zjuhzv2_space
Target Host: localhost
Target Database: zjuhzv2_space
Date: 2009/1/22 15:49:31
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for tb_comment
-- ----------------------------
CREATE TABLE `tb_comment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `nicky` tinyint(1) unsigned NOT NULL default '0',
  `deny` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_events
-- ----------------------------
CREATE TABLE `tb_events` (
  `tid` int(10) unsigned NOT NULL,
  `limit` int(10) unsigned default NULL,
  `time` int(10) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `member` text,
  `modtime` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_friends
-- ----------------------------
CREATE TABLE `tb_friends` (
  `uid` int(10) unsigned NOT NULL,
  `friend` int(10) unsigned NOT NULL,
  `sort` int(10) unsigned NOT NULL default '0',
  `type` char(6) NOT NULL default 'wait' COMMENT 'wait;pass;block',
  `time` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_friends_sort
-- ----------------------------
CREATE TABLE `tb_friends_sort` (
  `uid` int(10) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `name` char(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_group
-- ----------------------------
CREATE TABLE `tb_group` (
  `gid` int(10) unsigned NOT NULL auto_increment,
  `name` char(30) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `intro` text NOT NULL,
  `notice` text,
  `point` int(11) unsigned NOT NULL default '0',
  `type` char(5) NOT NULL default '',
  PRIMARY KEY  (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_group_member
-- ----------------------------
CREATE TABLE `tb_group_member` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `role` char(7) NOT NULL default '0' COMMENT 'creater;member;join;invite;manager',
  `lastvisit` int(10) unsigned default NULL,
  `jointime` int(10) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_help
-- ----------------------------
CREATE TABLE `tb_help` (
  `tid` int(10) unsigned NOT NULL,
  `sort` int(11) NOT NULL,
  `content` text NOT NULL,
  `memo` text,
  `state` tinyint(3) unsigned NOT NULL COMMENT '0:unsolved;1:solved;2:closed',
  `key` text COMMENT 'comment id which solved the problem'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_help_sort
-- ----------------------------
CREATE TABLE `tb_help_sort` (
  `sort` int(10) unsigned NOT NULL auto_increment,
  `name` char(30) default NULL,
  `rate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_home
-- ----------------------------
CREATE TABLE `tb_home` (
  `uid` int(10) unsigned NOT NULL,
  `ing` varchar(255) default NULL,
  `guests` text,
  `groups` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_msg
-- ----------------------------
CREATE TABLE `tb_msg` (
  `mid` bigint(20) unsigned NOT NULL auto_increment,
  `type` char(6) NOT NULL default 'pm' COMMENT 'pm;gbook;friend',
  `parent` bigint(20) unsigned NOT NULL default '0',
  `content` text,
  `sender` int(10) unsigned NOT NULL,
  `incept` int(11) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `isread` tinyint(1) unsigned NOT NULL default '0',
  `sbox` tinyint(1) unsigned NOT NULL default '1',
  `ibox` tinyint(1) unsigned NOT NULL default '1',
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
  `tags` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_news_sort
-- ----------------------------
CREATE TABLE `tb_news_sort` (
  `sort` int(10) unsigned NOT NULL auto_increment,
  `name` char(30) NOT NULL,
  `rate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sort`),
  UNIQUE KEY `ix_sortname` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_photo
-- ----------------------------
CREATE TABLE `tb_photo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tid` int(10) unsigned NOT NULL,
  `file` char(80) NOT NULL,
  `intro` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_share
-- ----------------------------
CREATE TABLE `tb_share` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tid` int(10) unsigned NOT NULL,
  `file` char(50) NOT NULL,
  `intro` tinytext,
  `download` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_tbar
-- ----------------------------
CREATE TABLE `tb_tbar` (
  `tid` int(10) unsigned NOT NULL auto_increment,
  `type` char(8) NOT NULL,
  `title` char(90) NOT NULL,
  `puber` int(10) unsigned NOT NULL,
  `pubtime` int(10) unsigned NOT NULL,
  `click` int(10) unsigned NOT NULL default '0',
  `reply` int(10) unsigned NOT NULL default '0',
  `rnicky` tinyint(1) NOT NULL default '0',
  `replyer` int(10) unsigned default NULL,
  `replytime` int(10) unsigned NOT NULL,
  `rate` int(10) unsigned NOT NULL default '0',
  `group` int(10) NOT NULL default '0',
  `deny` tinyint(1) unsigned NOT NULL default '0',
  `private` tinyint(3) unsigned NOT NULL COMMENT '0:self;1:friend;2:group;3:member;4:all',
  `nicky` tinyint(1) unsigned NOT NULL default '0',
  `ding` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  UNIQUE KEY `ix_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_tfav
-- ----------------------------
CREATE TABLE `tb_tfav` (
  `uid` int(10) unsigned NOT NULL,
  `topic` text,
  `news` text,
  `help` text,
  `photo` text,
  `events` text,
  `share` text,
  `vote` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_tjoin
-- ----------------------------
CREATE TABLE `tb_tjoin` (
  `uid` int(11) NOT NULL,
  `tid` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_topic
-- ----------------------------
CREATE TABLE `tb_topic` (
  `tid` int(10) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  `modtime` int(10) unsigned default NULL,
  UNIQUE KEY `ix_tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_vote
-- ----------------------------
CREATE TABLE `tb_vote` (
  `tid` int(10) unsigned NOT NULL,
  `options` text NOT NULL,
  `rates` text NOT NULL,
  `votenum` int(10) unsigned NOT NULL,
  `maxselect` tinyint(3) unsigned NOT NULL,
  `voters` longtext,
  `memo` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
