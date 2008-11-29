<?php

	class ErrorController extends Zend_Controller_Action 
	{
		function errorAction()
		{
			$error = $this->_getParam('error_handler');
			switch ($error->type)
			{
				case "EXCEPTION_NO_CONTROLLER" : 
				case "EXCEPTION_NO_ACTION" : 
					$this->view->message = '抱歉，您所请求的页面没找到。';
				break;
				
				default:
					$this->view->message = nl2br($error->exception->getMessage());
					$this->view->trace = nl2br($error->exception->getTraceAsString());
				break;
			}
			$this->view->type = $error->type;
			$this->view->custom = $this->_getParam('custom');
		}
		
		function denyAction()
		{
			
		}
	}

?>