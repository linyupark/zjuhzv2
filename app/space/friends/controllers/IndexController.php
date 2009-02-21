<?php

	/**
	 * 我的好友控制器
	 *
	 */
	class Space_Friends_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 好友tab选择
		 *
		 */
		function indexAction()
		{
			$type = $this->_getParam('type', 'list');
			$this->view->type = $type;
		}
		
		/**
		 * 好友访问记录
		 *
		 */
		function visitAction()
		{
			$uid = Cmd::uid();
			$visitor = Logic_Space_Home::get('guests', $uid);
			$visitor = unserialize($visitor['guests']);
			if($visitor != false)
			{
				$temp = array();
				foreach ($visitor as $v)
				{
					$temp[$v['uid']] = array(
						'time' => $v['time'], 
						'username' => $v['username'],
						'sex' => $v['sex']
					);
				}
				$friends = Logic_Space_Friends::ids($uid);
				$rows = array();
				foreach ($friends as $f)
				{
					if(array_key_exists($f['friend'], $temp))
					$rows[$f['friend']] = $temp[$f['friend']];
				}
				uasort($rows, create_function('$a, $b', 'return $a["time"] < $b["time"];'));
				
				$this->view->rows = $rows;
			}
		}
		
		/**
		 * 分组管理
		 *
		 */
		function sortAction()
		{
			$uid = Cmd::uid();
			$sorts = Logic_Space_Friends::getSort($uid);
			$default = Zend_Registry::get('config')->friends_sort->sid->toArray();
			$this->view->sorts = $sorts;
			$this->view->default = $default;
		}
		
		/**
		 * 好友分组属性表单
		 *
		 */
		function attrAction()
		{
			$fid = $this->_getParam('fid');
			$sid = $this->_getParam('sid');
			$this->view->friend = Logic_User_Base::get($fid);
			$this->view->sorts = Logic_Space_Friends::getSort(Cmd::uid());
			$this->view->sort = $sid;
		}
		
		/**
		 * 我的好友
		 *
		 */
		function listAction()
		{
			$uid = Cmd::uid();
			$page = $this->_getParam('p', 1);
			$sort = $this->_getParam('sort', 0);
			$friends = Logic_Space_Friends::fetch($uid, $sort);
			$pagesize = Alp_Page::$pagesize = 36;
			$sorts = Logic_Space_Friends::getSort($uid);
			$numrows = count($friends);
			
			if($numrows > $pagesize)
			{
				Alp_Page::create(array(
					'href_open' => '<a href="/space_friends/?sort='.$sort.'&p=%d">',
					'href_close' => '</a>',
					'cur_page' => $page,
					'num_rows' => $numrows
				));
				$friends = array_slice($friends, Alp_Page::$offset, $pagesize);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$this->view->page = $page;
			$this->view->rows = $friends;
			$this->view->sorts = $sorts;
			$this->view->sort = $sort;
			$this->view->numrows = $numrows;
		}
	}

?>