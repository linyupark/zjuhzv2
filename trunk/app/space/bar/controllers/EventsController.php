<?php

	/**
	 * 活动帖子
	 *
	 */
	class Space_Bar_EventsController extends Zend_Controller_Action
	{
		/**
		 * 报名人员列表
		 *
		 */
		function signboxAction()
		{
			$tid = $this->_getParam('tid');
			$this->view->members = Logic_Space_Bar_Events::members($tid);
		}
		
		/**
		 * 活动报名
		 *
		 */
		function signAction()
		{
			$row = $this->_getParam('row');
			$members = unserialize($row['member']);
			if($members == false) $members = array();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$flag = $this->_getParam('f');
				$tid = $this->_getParam('tid');
				$username = Cmd::getSess('profile', 'username');
				if($flag == 1) // 报名
				Logic_Space_Bar_Events::sign($tid, Cmd::uid(), $username);
				else 
				Logic_Space_Bar_Events::unsign($tid, Cmd::uid());
				
				echo 'success';
			}
			$this->view->limit = $row['limit'];
			$this->view->time = $row['time'];
			$this->view->members = $members;
			$this->view->tid = $row['tid'];
			$this->view->myid = Cmd::uid();
		}
		
		
		/**
		 * 查看活动详细
		 *
		 */
		function viewAction()
		{
			$page = $this->_getParam('p', 1);
			$tid = $this->_getParam('tid');
			$row = Logic_Space_Bar_Events::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('error', 'error', 'public');
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public');

			Logic_Space_Bar::click($tid);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
		
		/**
		 * 活动列表
		 *
		 */
		function indexAction()
		{
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$sort = $this->_getParam('sort', 'start');
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'zjuhzv2_space.tb_tbar'))
									  ->where('bar.group = ?', 0)
									  ->order('ding DESC');
			
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$select->joinLeft(array('e' => 'zjuhzv2_space.tb_events'), 'bar.tid = e.tid');
			
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('bar.type = ?', 'events');
					$select->where('bar.puber = ?', Cmd::uid());
				break;
				case 'join' : // 我参与的帖
					$row = Logic_Space_Bar::getJoin(Cmd::uid());
					if($row != false)
					{
						$tid_arr = unserialize($row['tid']);
						if(count($tid_arr) > 0)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('e.tid = ?', $tid);
								else $select->orWhere('e.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('e.tid = ?', 0);
					}
					else $select->where('e.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = Logic_Space_Bar::getFav('events', Cmd::uid());
					if($row != false)
					{
						$tid_arr = unserialize($row['events']);
						if(count($tid_arr) > 0 && $tid_arr != false)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('e.tid = ?', $tid);
								else $select->orWhere('e.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('e.tid = ?', 0);
					}
					else $select->where('e.tid = ?', 0);
				break;
				default: // 所有帖子
					$select->where('bar.type = ?', 'events');
				break;
			}
			
			switch ($sort)
			{
				case 'start' : // 即将开始
					$select->where('e.time > '.time());
					$select->order('e.time ASC');
				break;
				case 'over' : // 最近结束
					$select->where('e.time < '.time());
					$select->order('e.time DESC');
				break;
				default : 
				break;
			}
			
			switch ($order)
			{
				case 'time' : // 发布时间
					$select->order('bar.pubtime DESC');
				break;
				case 'rtime' : // 回复时间
					$select->order('bar.replytime DESC');
				break;
				case 'reply' : // 回复数
					$select->order('bar.reply DESC');
				break;
				case 'click' : // 点击率
					$select->order('bar.click DESC');
				break;
				default : // 被顶数
					$select->order('bar.rate DESC');
				break;
			}

			$rows = $select->query()->fetchAll();
			$pagesize = Alp_Page::$pagesize = 10;
			if(count($rows) > $pagesize)
			{
				Alp_Page::create(array(
					'href_open' => '<a href="?type=news&order='.$order.'&where='.$where.'&sort='.$sort.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => count($rows),
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$rows = $select->query()->fetchAll();
				$this->view->pagination = Alp_Page::$page_str;
			}
			$this->view->rows = $rows;
			$this->view->where = $where;
			$this->view->sort = $sort;
		}
		
		function sortlistAction()
		{
			
		}
	}

?>