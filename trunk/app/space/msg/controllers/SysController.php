<?php

	/**
	 * 系统站内信控制器
	 *
	 */
	class Space_Msg_SysController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 发送好友申请信息
		 *
		 */
		function friendAction()
		{
			$params = $this->view->params;
			$sender = Logic_User_Base::get($params['sender']);
			$incept = $params['incept'];
			$content = $sender['username'].'请求加你为好友。';
		}
	}

?>