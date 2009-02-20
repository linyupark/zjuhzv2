<?php

	/**
	 * 查找中心控制器
	 *
	 */
	class Space_Search_IndexController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$this->view->for = $this->_getParam('for', 'bar');
		}
	}

?>