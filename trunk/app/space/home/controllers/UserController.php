<?php

	/**
	 * 访问用户主页
	 *
	 */
	class Space_Home_UserController extends Zend_Controller_Action 
	{		
		function indexAction()
		{
			$uid = $this->_getParam('uid');
			$this->view->profile = Logic_User_Base::get($uid);
			$this->view->home = Logic_User_Privacy::getHome($uid);
			$this->view->uid= $uid;
		}
	}

?>