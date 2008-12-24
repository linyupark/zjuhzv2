<?php

	/**
	 * 新闻帖子
	 *
	 */
	class Space_Bar_NewsController extends Zend_Controller_Action
	{
		/**
		 * 新闻列表
		 *
		 */
		function indexAction()
		{
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$sort = $this->_getParam('sort', 'all');
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'zjuhzv2_space.tb_tbar'))
									  ->where('`group` = ?', 0)
									  ->where('type = ?', 'news');
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
					$row = Logic_Space_Bar::getFav('topic', Cmd::uid());
					if($row != false)
					{
						$tid_arr = unserialize($row['topic']);
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
				default: // 所有帖子
					
				break;
			}
			switch ($order)
			{
				case 'time' : // 发布时间
					$select->order('pubtime DESC');
				break;
				case 'reply' : // 回复数
					$select->order('reply DESC');
				break;
				case 'click' : // 点击率
					$select->order('click DESC');
				break;
				default : // 被顶数
					$select->order('rate DESC');
				break;
			}
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$select->joinLeft(array('news' => 'zjuhzv2_space.tb_news'), 'bar.tid = news.tid',
							  array('sid' => 'news.sort'));
			$select->joinLeft(array('s' => 'zjuhzv2_space.tb_news_sort'), 'news.sort = s.sort',
							  array('sortname' => 's.name'));
			if($sort != 'all') $select->where('news.sort = ?', $sort);
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
		
		function viewAction()
		{
			$page = $this->_getParam('p', 1);
			$tid = $this->_getParam('tid');
			$row = Logic_Space_Bar_News::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('error', 'error', 'public');
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public');

			Logic_Space_Bar::click($tid);
			$this->view->row = $row[0];
			$this->view->page = $page;
		}
		
		/**
		 * 新闻分类模块调用
		 *
		 */
		function sortsAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->view->sorts = Logic_Space_Bar_News::getSorts(); // 获取新闻分类
				$this->render('sorts');
			}
		}
		
		/**
		 * 新闻分类
		 *
		 */
		function sortlistAction()
		{
			$sorts = Logic_Space_Bar_News::getSorts();
			$this->view->sorts = $sorts;
		}
		
		/**
		 * 创建新新闻分类
		 *
		 */
		function createsortAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$sortname = Filter_Space::newsSort($this->_getParam('sortname'));
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_News::createSort($sortname);
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