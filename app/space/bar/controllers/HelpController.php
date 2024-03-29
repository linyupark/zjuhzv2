<?php

	/**
	 * 求助帖子
	 *
	 */
	class Space_Bar_HelpController extends Zend_Controller_Action
	{
		function memoAction()
		{
			$tid = $this->_getParam('tid');
			$memo = $this->_getParam('memo');
			if($this->getRequest()->isXmlHttpRequest())
			{
				$data = Alp_String::html(trim($this->_getParam('data')));
				Logic_Space_Bar_Help::memo($tid, $data);
			}
			$this->view->tid = $tid;
			$this->view->memo = $memo;
		}
		
		/**
		 * 求助状态
		 *
		 */
		function stateboxAction()
		{
			$tid = $this->_getParam('tid');
			$state = $this->_getParam('state');
			Logic_Space_Bar_Help::state($tid, $state);
			$this->view->state = $state;
		}
		
		/**
		 * 求助列表
		 *
		 */
		function indexAction()
		{
			$myid = Cmd::uid();
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$sort = $this->_getParam('sort', 'all');
			$state = $this->_getParam('state', 'unsolved');
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'tb_tbar'), 
											 array('numrows' => new Zend_Db_Expr('COUNT(bar.tid)')))
									  ->where('bar.group = 0')->where('bar.deny = 0')
									  ->where('bar.type = ?', 'help')
									  ->order('bar.ding DESC');
			
			$select->joinLeft(array('help' => 'tb_help'), 'bar.tid = help.tid', array());
			
			switch ($state)
			{
				case 'unsolved' : // 待解决
					$select->where('help.state = ?', 0);
				break;
				case 'solved' : // 已解决
					$select->where('help.state = ?', 1);
				break;
				default: break;
			}
							  
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
							$select->where('help.tid IN ('.$in_tid.')');
						}
						else $select->where('help.tid = ?', 0);
					}
					else $select->where('help.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = Logic_Space_Bar::getFav('help', $myid);
					if($row != false)
					{
						$tid_arr = unserialize($row['help']);
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
							$select->where('help.tid IN ('.$in_tid.')');
						}
						else $select->where('help.tid = ?', 0);
					}
					else $select->where('help.tid = ?', 0);
				break;
				default: // 所有帖子
				break;
			}
			
			if($sort != 'all') $select->where('help.sort = ?', $sort);
			
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
			$pagesize = 10;
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::create(array(
					'href_open' => '<a href="?type=help&order='.$order.'&where='.$where.'&sort='.$sort.'&state='.$state.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->joinLeft(array('s' => 'zjuhzv2_space.tb_help_sort'), 'help.sort = s.sort',
							  array('sortname' => 's.name'));
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			
			$rows = $select->query()->fetchAll();
			$this->view->rows = $rows;
			$this->view->where = $where;
			$this->view->sort = $sort;
			$this->view->state = $state;
		}
		
		function viewAction()
		{
			$page = $this->_getParam('p', 1);
			$tid = $this->_getParam('tid');
			$row = Logic_Space_Bar_Help::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('index', 'index', 'public');
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public', 
					array('position' => 'space_bar', 'private' => $row[0]['private']));

			Logic_Space_Bar::click($tid);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
		
		function sortlistAction()
		{
			$sorts = Logic_Space_Bar_Help::getSorts();
			$this->view->sorts = $sorts;
		}
		
		/**
		 * 求助分类模块调用
		 *
		 */
		function sortsAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->view->sorts = Logic_Space_Bar_Help::getSorts(); // 获取新闻分类
				$this->render('sorts');
			}
		}
		
		/**
		 * 创建新求助分类
		 *
		 */
		function createsortAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$role = Cmd::role();
				if($role != 'master' && $role != 'power')
				Alp_Sys::msg('deny', '您目前所在用户组无法创建新分类，如有需要请通过：'."\n".
					'"全站搜索"=>"校友"=>"范围:管理员"使用站内信联系管理员');
				$sortname = Filter_Space::helpSort($this->_getParam('sortname'));
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Help::createSort($sortname);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
	}

?>