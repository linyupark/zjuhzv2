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
			$this->view->type = 'send';
		}
		
		/**
		 * 短消息发送
		 *
		 */
		function postAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = $this->_getAllParams();
			$params = Filter_Msg::pm($params);
			if(Alp_Sys::getMsg() == null)
			{
				if($params['incept'] == '') // 从好友名字中获取id
				{
					$unames = explode(' ', $params['uname']);
					Zend_Debug::dump(Logic_User_Base::uid($unames));
				}
			}
			echo Alp_Sys::allMsg('* ', "\n");
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