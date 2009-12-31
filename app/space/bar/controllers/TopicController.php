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
			$myid = Cmd::uid();
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()->from(array('bar' => 'tb_tbar'), 
													   array('numrows' => new Zend_Db_Expr('COUNT(bar.tid)')))
												->where('bar.group = 0')->where('bar.deny = 0')
												->order('ding DESC');
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('puber = ?', $myid);
				break;
				case 'join' : // 我参与的帖
					$row = Logic_Space_Bar::getJoin($myid);
					if($row != false)
					{
						$tid_arr = unserialize($row['tid']);
						if(count($tid_arr) > 0)
						{
							$in_tid = '';
							foreach ($tid_arr as $tid => $time)
							{
								$in_tid .= $tid.',';
							}
							$in_tid = substr($in_tid, 0, -1);
							$select->where('bar.tid IN ('.$in_tid.')');
						}
						else $select->where('bar.tid = ?', 0);
					}
					else $select->where('bar.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = DbModel::Space()->fetchRow('SELECT * FROM `tb_tfav` WHERE `uid` = ?', $myid);
					if($row != false)
					{
						$tid_arr = array();
						foreach ($row as $r)
						{
							if(unserialize($r)) $tid_arr[] = unserialize($r);
						}
						if(count($tid_arr) > 0)
						{
							$in_tid = '';
							foreach ($tid_arr as $v)
							{
								$tid = array_keys($v);
								foreach ($tid as $id)
								$in_tid .= $id.',';
							}
							$in_tid = substr($in_tid, 0, -1);	
							$select->where('bar.tid IN ('.$in_tid.')');
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
                    $select->order('bar.reply DESC');
					//$select->order('bar.rate DESC');
				break;
			}
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			$pagesize = 10;
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="?type=topic&order='.$order.'&where='.$where.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$rows = $select->query()->fetchAll();
			
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
			// 有对应帖子判断阅读权限
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'], $row[0]['group'])) 
			$this->_forward('deny', 'error', 'public', 
					array('position' => 'space_bar', 'private' => $row[0]['private']));

			Logic_Space_Bar::click($tid, $row[0]['group']);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
	}

?>