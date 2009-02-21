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
			$this->view->key = trim(urldecode($this->_getParam('key')));
			$this->view->range = $this->_getParam('range', 'all'); // 查找范围
			$this->view->attr = $this->_getParam('attr', 'username'); // 查找属性
		}
	}

?>