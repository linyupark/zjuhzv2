<?php

	/**
	 * 设置隐私控制
	 *
	 */
	class Space_Set_PrivacyController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->headTitle('资料隐私设置');
			$this->view->controller_name = 'privacy';
		}
		
		function indexAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				Logic_User_Privacy::setHome($params['home'], Cmd::uid());
				unset($params['home']);
				unset($params['module']);
				unset($params['controller']);
				unset($params['action']);
				Logic_User_Privacy::setAccess($params, Cmd::uid());
				if(Alp_Sys::getMsg() == null)
				echo 'success';
				else echo Alp_Sys::allMsg('* ',"\n");
			}
			$this->view->access = Logic_User_Privacy::getAccess(Cmd::uid());
			$this->view->home = Logic_User_Privacy::getHome(Cmd::uid());
			$this->view->profile = Cmd::getSess('profile');
		}
	}

?>