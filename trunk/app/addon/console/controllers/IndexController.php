<?php

	class Addon_Console_IndexController extends Zend_Controller_Action 
	{
		private $key;
		
		function init()
		{
			$this->key = Zend_Registry::get('config')->addon_console->key;
		}
		
		/**
		 * 附加组件管理登陆
		 *
		 */
		function indexAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$key = md5($this->getRequest()->getParam('key'));
                        echo $key.':'.$this->key;
			if($key == $this->key)
			{
                                echo 'go';
				//Cmd::setSess('addon_master', $key);
				//$this->_redirect('/addon_console/panel');
			}
		}
	}

?>