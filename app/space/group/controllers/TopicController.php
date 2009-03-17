<?php

	/**
	 * 群组话题控制器
	 *
	 */
	class Space_Group_TopicController extends Zend_Controller_Action 
	{		
		function init()
		{
			$this->view->gid = $this->_getParam('id');
			$this->view->tab = $this->getRequest()->getControllerName();
		}
		
		function historyAction()
		{
			$history = Cmd::getSess('group_'.$this->view->gid.'_history');
			if($history != null)
			{
				arsort($history); // 根据浏览更新时间来排序
				$select = DbModel::Space()->select()->from('tb_tbar');
				$i = 0;
				foreach ($history as $tid => $time)
				{
					if($i == 9) break;
					if($i == 0) $select->where('tid = ?', $tid);
					else $select->orWhere('tid = ?', $tid);
					$i++;
				}
				$history = $select->query()->fetchAll();
			}
			$this->view->history = $history;
		}
		
		function indexAction()
		{
			$uid = Cmd::uid();
			$gid = $this->view->gid;
			$group = Logic_Space_Group::info($gid);
			if(Logic_Space_Group::isAllowedVisit($gid, $uid))
			{	
				$range = $this->_getParam('range', 'all');
				$where = $this->_getParam('where', 'all');
				$order = $this->_getParam('order', 'time');
				$page = $this->_getParam('p', 1); // 默认显示页
				$select = DbModel::Space()->select()->from(array('bar' => 'tb_tbar'), 
													   array('numrows' => new Zend_Db_Expr('COUNT(bar.tid)')))
													->where('bar.group = ?', $gid)->where('bar.deny = 0')
													->order('ding DESC');
				// 范围
				if($range != 'all')
				{
					$select->where('bar.type = ?', $range);
				}
													
				switch ($where)
				{
					case 'pub' : // 我发布的帖子
						$select->where('puber = ?', $uid);
					break;
					case 'join' : // 我参与的帖
						$row = Logic_Space_Bar::getJoin($uid);
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
						$select->order('bar.rate DESC');
					break;
				}
				$row = $select->query()->fetchAll();
				$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
				
				$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
								  array('pubname' => 'username', 'pubnick' => 'nickname'));
				$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
								  array('replyname' => 'username', 'replynick' => 'nickname'));
				
				$pagesize = Alp_Page::$pagesize = 20;
				$numrows = $row[0]['numrows'];
				if($numrows > $pagesize)
				{
					Alp_Page::create(array(
						'href_open' => '<a href="?id='.$gid.'&type=topic&order='.$order.'&where='.$where.'&p=%d">',
						'href_close' => '</a>',
						'num_rows' => $numrows,
						'cur_page' => $page
					));
					$select->limit($pagesize, Alp_Page::$offset);
					$this->view->pagination = Alp_Page::$page_str;
				}
				$this->view->rows = $select->query()->fetchAll();
				$this->view->where = $where;
				$this->view->order = $order;
				$this->view->range = $range;
				$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
				$this->view->group = $group;
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
	}
?>