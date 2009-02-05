<?php

	/**
	 * 我的好友控制器
	 *
	 */
	class Space_Friends_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 好友tab选择
		 *
		 */
		function indexAction()
		{
			$type = $this->_getParam('type', 'list');
			$this->view->type = $type;
		}
		
		function listAction()
		{
			
		}
	}

?>