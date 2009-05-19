<?php

	/**
	 * 个人首页扩展信息控制器
	 *
	 */
	class Space_Home_ExtController extends Zend_Controller_Action 
	{
		private $uid;
		private $db;
		private $limit = 5;
		
		function init()
		{
			$this->db = DbModel::Space();
			$this->uid = $this->_getParam('uid', Cmd::uid());
		}
		
		/**
		 * 最近去过的我的群组
		 *
		 */
		function recentgroupAction()
		{
			$rows = $this->db->fetchAll('
				SELECT g.`name`,m.`gid` 
				FROM `tb_group_member` AS `m` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = m.`gid` 
				WHERE m.`uid` = ? 
				ORDER BY `lastvisit` DESC 
				LIMIT 5
			', $this->uid);
			$this->view->rows = $rows;
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
		 * 屏蔽发布的帖子
		 *
		 */
		function bardenyAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$db = DbModel::Space();
				$tid = $this->_getParam('tid');
				$row = $db->fetchRow('SELECT `puber` FROM `tb_tbar` WHERE `puber` = ? AND `tid` = '.$tid, Cmd::uid());
				if($row != false)
				{
					$db->update('tb_tbar', array('deny' => 1), 'tid = '.$tid);
					echo 'success';
				}
				else echo '无权屏蔽';
			}
		}
		
		/**
		 * 个人所有回复/帖子
		 *
		 */
		function barAction()
		{
			$page = $this->_getParam('p', 1);
			$pagesize = 20;
			$uid = $this->_getParam('uid', Cmd::uid());
			$tab = $this->_getParam('tab', 'pub');
			// 发布
			if($tab == 'pub'):
			$tids = Logic_Space_Bar::ids($uid);
			$numrows = count($tids);
			$offset = 0;
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_home/ext/bar/?tab='.$tab.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$this->view->pagination = Alp_Page::$page_str;
				$offset = Alp_Page::$offset;
			}
			if($numrows > 0)
			{
				$rows = DbModel::Space()->fetchAll('SELECT * 
					FROM `tb_tbar` WHERE `puber` = '.$uid.' 
					AND `deny` = 0 
					ORDER BY `replytime` DESC LIMIT '.$offset.','.$pagesize);
				$this->view->pubs = $rows;
			}
			endif;
			// 回复
			if($tab == 'rep'):
			$db = DbModel::Space();
			$row = $db->fetchRow('SELECT COUNT(`id`) AS `numrows` FROM `tb_comment` WHERE `uid` = ?', $uid);
			$numrows = $row['numrows'];
			$select = $db->select()->from(array('c' => 'tb_comment'))->where('uid = '.$uid);
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_home/ext/bar/?tab='.$tab.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			if($numrows > 0)
			{
				$select->joinLeft(array('t' => 'tb_tbar'), 't.tid = c.tid', array('t.title','t.type'));
				$select->order('c.time DESC');
				$rows = $select->query()->fetchAll();
				$this->view->reps = $rows;
			}
			endif;
			
			$this->view->numrows = $numrows;
			$this->view->tab = $tab;
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
	}

?>