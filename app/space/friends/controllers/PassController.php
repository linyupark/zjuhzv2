<?php

	/**
	 * 好友通过控制器
	 *
	 */
	class Space_Friends_PassController extends Zend_Controller_Action 
	{	
		/**
		 * 执行通过请求
		 *
		 */
		function indexAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->_getAllParams();
				$params = Filter_Friends::pass($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Friends::rel($params['sender'], $params['uid']);
					Logic_Space_Friends::pass($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
			
		}
		
		/**
		 * 好友通过表单(好友归类)
		 *
		 */
		function formAction()
		{
			$this->view->sorts = Logic_Space_Friends::getSort(Cmd::uid());
			$this->view->sender = $this->_getParam('sender');
		}
	}

?>