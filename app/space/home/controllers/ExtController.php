<?php

	/**
	 * 个人首页扩展信息控制器
	 *
	 */
	class Space_Home_ExtController extends Zend_Controller_Action 
	{
		private $uid;
		private $db;
		private $limit = 10;
		
		function init()
		{
			$this->db = DbModel::Space();
			$this->uid = $this->_getParam('uid', Cmd::uid());
		}
		
		/**
		 * 指定用户的最近发布
		 *
		 */
		function recentpostAction()
		{
			$rows = $this->db->fetchAll('
				SELECT b.`type`,b.`tid`,b.`pubtime`,b.`group`,b.`reply`,b.`click`,b.`title`,g.`name` 
				FROM `tb_tbar` AS `b`  
				LEFT JOIN `tb_group` AS `g` ON `g`.`gid` = `group` 
				WHERE b.`puber` = ? AND b.`deny` = 0 
				ORDER BY b.`pubtime` DESC 
				LIMIT '.$this->limit, $this->uid);
			
			$this->view->rows = $rows;
		}
		
		/**
		 * 指定用户的最近回复
		 *
		 */
		function recentreplyAction()
		{
			$rows = $this->db->fetchAll('
				SELECT DISTINCT b.`type`,c.`tid`,b.`title`,b.`group`,b.`reply`,b.`click`,g.`name` 
				FROM `tb_comment` AS `c` 
				LEFT JOIN `tb_tbar` AS `b` ON `b`.`tid` = `c`.`tid` 
				LEFT JOIN `tb_group` AS `g` ON `g`.`gid` = `b`.`group` 
				WHERE c.`uid` = ? AND c.`deny` = 0 
				ORDER BY c.`time` DESC 
				LIMIT '.$this->limit, $this->uid);
			
			$this->view->rows = $rows;
		}
		
		/**
		 * 个人所有回复/帖子
		 *
		 */
		function barAction()
		{
			
		}
	}

?>