<?php

	class Space_Bar_IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->type = $this->_getParam('type', 'topic');
			$this->view->order = $this->_getParam('order', 'time'); // 排序方式
			$this->view->pub = $this->_getParam('pub'); // 发布什么
		}
		
		function indexAction()
		{
			
		}
	}

?>