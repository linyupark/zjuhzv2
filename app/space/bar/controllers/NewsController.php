<?php

	/**
	 * 新闻帖子
	 *
	 */
	class Space_Bar_NewsController extends Zend_Controller_Action
	{
		/**
		 * 上下文章
		 *
		 */
		function prenextAction()
		{
			$tid = $this->_getParam('tid');
			$this->view->pre = DbModel::Space()->fetchRow('
				SELECT `title`,`tid` FROM `tb_tbar` 
				WHERE `tid` < '.$tid.'
				AND `type` = "news" ORDER BY `pubtime` DESC');
			$this->view->next = DbModel::Space()->fetchRow('
				SELECT `title`,`tid` FROM `tb_tbar` 
				WHERE `tid` > '.$tid.'
				AND `type` = "news" ORDER BY `pubtime` ASC');
		}
		
		/**
		 * 相关文章
		 *
		 */
		function relativeAction()
		{
			$tags = $this->_getParam('tags');
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'tb_tbar'))
									  ->where('bar.group = 0 AND bar.type = "news" AND bar.tid != ?', $this->_getParam('tid'));
			$sql = '';
			foreach ($tags as $i => $t)
			{
				$sql .= 'bar.title LIKE "%'.$t.'%"';
				if(($i+1) < count($tags)) $sql .= ' OR ';
			}
			$select->where($sql)->order('pubtime DESC')->limit(5);
			$rows = $select->query()->fetchAll();
			$this->view->rows = $rows;
			$this->view->tags = $tags;
		}
		
		/**
		 * 新闻列表
		 *
		 */
		function indexAction()
		{
			$myid = Cmd::uid();
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$sort = $this->_getParam('sort', 'all');
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()
									  ->from(array('bar' => 'zjuhzv2_space.tb_tbar'), 
											 array('numrows' => new Zend_Db_Expr('COUNT(bar.tid)')))
									  ->where('bar.group = 0')->where('bar.deny = 0')
									  ->order('bar.ding DESC')->group('bar.tid');
			
			$select->joinLeft(array('news' => 'zjuhzv2_space.tb_news'), 'bar.tid = news.tid');
			
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('bar.type = ?', 'news');
					$select->where('bar.puber = ?', $myid);
				break;
				case 'join' : // 我参与的帖
					$row = Logic_Space_Bar::getJoin($myid);
					if($row != false)
					{
						$tid_arr = unserialize($row['tid']);
						if(count($tid_arr) > 0)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('news.tid = ?', $tid);
								else $select->orWhere('news.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('news.tid = ?', 0);
					}
					else $select->where('news.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = Logic_Space_Bar::getFav('news', $myid);
					if($row != false)
					{
						$tid_arr = unserialize($row['news']);
						if(count($tid_arr) > 0 && $tid_arr != false)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('news.tid = ?', $tid);
								else $select->orWhere('news.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('news.tid = ?', 0);
					}
					else $select->where('news.tid = ?', 0);
				break;
				default: // 所有帖子
					$select->where('bar.type = ?', 'news');
				break;
			}
			
			if($sort != 'all') $select->where('news.sort = ?', $sort);
			
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
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="?type=news&order='.$order.'&where='.$where.'&sort='.$sort.'&p=%d">',
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
			$select->joinLeft(array('s' => 'zjuhzv2_space.tb_news_sort'), 'news.sort = s.sort',
							  array('sortname' => 's.name', 'sid' => 's.sort'));
							  
			$rows = $select->query()->fetchAll();
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
			$this->_forward('deny', 'error', 'public', 
					array('position' => 'space_bar', 'private' => $row[0]['private']));

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
				$this->view->sid = $this->_getParam('sort');
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
				$role = Cmd::role();
				if($role != 'master' && $role != 'power')
				Alp_Sys::msg('deny', '您目前所在用户组无法创建新分类，如有需要请通过：'."\n".
					'"全站搜索"=>"校友"=>"范围:管理员"使用站内信联系管理员');
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