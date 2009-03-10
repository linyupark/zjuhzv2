<?php

	/**
	 * 来自某人的好友控制器
	 *
	 */
	class Space_Friends_FromController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$uid = (int)$this->_getParam('uid', 0);
			if($uid == 0 || $uid == Cmd::uid()) $this->_redirect('/space_friends/');
			
			$page = $this->_getParam('p', 1);
			$pagesize = 12;
			$friends = Logic_Space_Friends::fetch($uid);
			$numrows = count($friends);
			
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_friends/from/?uid='.$uid.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$this->view->friends = array_slice($friends, Alp_Page::$offset, $pagesize);
				$this->view->pagination = Alp_Page::$page_str;
			}
			else $this->view->friends = $friends;
			
			// 当前用户的基本信息
			$this->view->user = Logic_User_Base::get($uid);
			$this->view->numrows = $numrows;
		}
	}

?>