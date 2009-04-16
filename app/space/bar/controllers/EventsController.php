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
		
		function aptAction()
		{
			$pt = 5;
			$uids = $this->_getParam('uid');
			$memo = strip_tags(trim($this->_getParam('memo')));
			$tid = $this->_getParam('tid');
			$e = Logic_Space_Bar_Events::view($tid);
			$puber = $e[0]['puber'];
			if($uids == null && $memo == null)
			{
				$uids = Logic_Space_Bar_Events::members($tid);
				$this->view->memo = $e[0]['title'];
				$this->view->uids = $uids;
				$this->view->tid = $tid;
				
			} else { // 处理加分
				$this->getHelper('viewRenderer')->setNoRender();
				if(count($uids) > 0 && $memo != null)
				{
					foreach ($uids as $uid)
					{
						Logic_Api::apoint('user', $uid, $pt, $memo, time());
					}
					DbModel::Space()->update('tb_events', array('apted' => 1), 'tid = '.$tid);
					Logic_Api::apoint('user', $puber, 10, '成功发起活动'.$e[0]['title'], time());
					$puber == Cmd::uid() ? Cmd::setSess('apt_tip', array('pubevents' => 10)) : '';
					echo 'success';
				}
				else echo '加分必要数据不全';
			}
		}
		
		/**
		 * 导出excel
		 *
		 */
		function xlsAction()
		{
			$tid = $this->_getParam('tid');
			$row = Logic_Space_Bar_Events::view($tid);
			$uid = Cmd::uid(); $role = Cmd::role();
			$grole = '';
			if($row[0]['group'] > 0)  $grole = Logic_Space_Group_Member::role($row[0]['group'],$uid);
			// 判断是否有条件进行导出
			if($role == 'master' || $row[0]['puber'] == $uid || $grole == 'creater' || $grole == 'manager')
			$this->_forward('events','excel','api_export', array('tid'=>$tid));
		}
		
		/**
		 * 活动报名
		 *
		 */
		function signAction()
		{
			$myid = Cmd::uid();
			$role = Cmd::role();
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
				{
					if(!Cmd::getSess('profile', 'mobile'))
                    {
                        echo '您的资料里没有填写手机信息无法报名';
                    }
                    else
                    {
                    	Logic_Space_Bar_Events::sign($tid, $myid, $username);
                    	// 记录
                    	echo 'success';
                    }
				}
				else 
				{
					Logic_Space_Bar_Events::unsign($tid, $myid);
					echo 'success';
				}
			}
			// 允许进行加分操作
			if($role == 'master' || $myid == $row['puber'])
			$this->view->apt = $row['apted'] == 1 ? false : true;
			$this->view->apt_m = $row['apted'];
			$this->view->limit = $row['limit'];
			$this->view->time = $row['time'];
			$this->view->members = $members;
			$this->view->tid = $row['tid'];
			$this->view->myid = $myid;
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
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'], $row[0]['group'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public', 
					array('position' => 'space_bar', 'private' => $row[0]['private']));

			Logic_Space_Bar::click($tid, $row[0]['group']);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
		
		/**
		 * 活动列表
		 *
		 */
		function indexAction()
		{
			$myid = Cmd::uid();
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$sort = $this->_getParam('sort', 'start');
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'tb_tbar'), 
											 array('numrows' => new Zend_Db_Expr('COUNT(bar.tid)')))
									  ->where('bar.deny = 0 AND bar.type = "events"')
									  ->order('bar.ding DESC');
									  
			$select->joinLeft(array('e' => 'tb_events'), 'bar.tid = e.tid', array());
			
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('bar.puber = ?', $myid);
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
							$select->where('e.tid IN ('.$in_tid.')');
						}
						else $select->where('e.tid = ?', 0);
					}
					else $select->where('e.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = Logic_Space_Bar::getFav('events', $myid);
					if($row != false)
					{
						$tid_arr = unserialize($row['events']);
						if(count($tid_arr) > 0 && $tid_arr != false)
						{
							$in_tid = '';
							foreach ($tid_arr as $v)
							{
								$tid = array_keys($v);
								foreach ($tid as $id)
								$in_tid .= $id.',';
							}
							$in_tid = substr($in_tid, 0, -1);	
							$select->where('e.tid IN ('.$in_tid.')');
						}
						else $select->where('e.tid = ?', 0);
					}
					else $select->where('e.tid = ?', 0);
				break;
				default: // 所有帖子
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
			
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns(array('bar.*','e.*'));
			$pagesize = 10;
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="?type=events&order='.$order.'&where='.$where.'&sort='.$sort.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname', 'pubsex' => 'puser.sex'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
							  
			$rows = $select->query()->fetchAll();
			$this->view->rows = $rows;
			$this->view->where = $where;
			$this->view->sort = $sort;
		}
		
		function sortlistAction()
		{
			
		}
	}

?>