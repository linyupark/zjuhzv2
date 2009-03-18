<?php
	/**
	 * 邮箱订阅控制器
	 *
	 */
	class Space_Rss_EmailController extends Zend_Controller_Action 
	{
		function init(){ $this->view->tab = $this->getRequest()->getControllerName(); }
		
		function indexAction()
		{
			
		}
	}
	
?>