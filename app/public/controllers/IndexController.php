<?php

	class IndexController extends Zend_Controller_Action 
	{
		function indexAction()
		{
		}
		
		function docAction()
		{
			$this->view->of = $this->_getParam('of');
		}
	}

?>