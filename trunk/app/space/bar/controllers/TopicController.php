<?php

	/**
	 * 公共话题
	 *
	 */
	class Space_Bar_TopicController extends Zend_Controller_Action
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 话题列表
		 *
		 */
		function indexAction()
		{
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()->from(array('bar' => 'zjuhzv2_space.tb_tbar'))
												->where('`group` = ?', 0)
												->order('ding DESC');
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('puber = ?', Cmd::uid());
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
								if($i == 0) $select->where('bar.tid = ?', $tid);
								else $select->orWhere('bar.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('bar.tid = ?', 0);
					}
					else $select->where('bar.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = DbModel::Space()->fetchRow('SELECT * FROM `tb_tfav` WHERE `uid` = ?', Cmd::uid());
					if($row != false)
					{
						$tid_arr = array();
						foreach ($row as $r)
						{
							if(unserialize($r)) $tid_arr[] = unserialize($r);
						}
						
						if(count($tid_arr) > 0)
						{
							$i = 0;
							foreach ($tid_arr as $v)
							{
								if($i == 0) $select->where('bar.tid = ?', array_keys($v));
								else $select->orWhere('bar.tid = ?',  array_keys($v));
								$i++;
							}
						}
						else $select->where('bar.tid = ?', 0);
					}
					else $select->where('bar.tid = ?', 0);
				break;
				default: // 所有帖子
					
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
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$rows = $select->query()->fetchAll();
			$pagesize = Alp_Page::$pagesize = 10;
			if(count($rows) > $pagesize)
			{
				Alp_Page::create(array(
					'href_open' => '<a href="?type=topic&order='.$order.'&where='.$where.'&p=%d">',
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
		}
		
		/**
		 * 详细信息
		 *
		 */
		function viewAction()
		{
			$page = $this->_getParam('p', 1);
			$tid = $this->params['tid'];
			$row = Logic_Space_Bar_Topic::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('error', 'error', 'public');
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public');

			Logic_Space_Bar::click($tid);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
	}

?>