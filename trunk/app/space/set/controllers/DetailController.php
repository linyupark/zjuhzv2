<?php

	/**
	 * 个人详细信息控制器
	 *
	 */
	class Space_Set_DetailController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->marriage = Zend_Registry::get('config')->marriage->v->toArray();
		}
		
		function indexAction()
		{
			$uid = $this->_getParam('uid', Cmd::uid());
			$this->view->base = Logic_User_Base::get($uid);
		}
		
		/**
		 * 查看自己的详细资料
		 *
		 */
		function myAction()
		{
			$this->view->data = Logic_Api::user(Cmd::uid());
		}
		
		/**
		 * 访问别人的资料
		 *
		 */
		function userAction()
		{
			$uid = $this->_getParam('uid');
			if(Logic_Space_Friends::hasFriend($uid, Cmd::uid()))
			$this->view->rel = 1;
			else $this->view->rel = 10;
			$this->view->data = Logic_Api::user($uid);
			$this->view->uid = $uid;
		}
	}
	
?>