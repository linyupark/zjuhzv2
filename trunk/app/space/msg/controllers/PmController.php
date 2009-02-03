<?php

	/**
	 * 站内短消息控制器
	 *
	 */
	class Space_Msg_PmController extends Zend_Controller_Action 
	{	
		/**
		 * 发送站内信表单
		 *
		 */
		function indexAction()
		{
			$uid = $this->_getParam('uid');
			$incept = Logic_User_Base::get($uid);
			$this->view->incept = $incept;
		}
		
		/**
		 * 好友列表
		 *
		 */
		function friendsAction()
		{
			$this->view->sorts = Logic_Space_Friends::getSort(Cmd::uid());
		}
	}

?>