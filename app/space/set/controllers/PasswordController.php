<?php

	/**
	 * 设置账号密码
	 *
	 */
	class Space_Set_PasswordController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->headTitle('设置密码');
			$this->view->controller_name = 'password';
		}
		
		function indexAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				Filter_User::password($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_User_Password::update($params, Cmd::uid());
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