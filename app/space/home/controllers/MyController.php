<?php

	/**
	 * 个人主页
	 *
	 */
	class Space_Home_MyController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->headTitle('我的个人主页');
		}
		
		function doingAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$ing = strip_tags(trim($this->_getParam('ing')));
			Logic_Space_Home::ing($ing, Cmd::uid());
		}
		
		function indexAction()
		{
			$this->view->profile = Cmd::getSess('profile');
			$this->view->home = Logic_User_Privacy::getHome(Cmd::uid());
		}
	}

?>