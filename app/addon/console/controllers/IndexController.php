<?php

	class Addon_Console_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 附加组件管理登陆
		 *
		 */
		function indexAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$key = md5($this->getRequest()->getParam('key'));
                        $rkey = Zend_Registry::get('config')->addon_console->key;
                        echo $rkey;
			if($key == $rkey)
			{
                                echo 'go';
				//Cmd::setSess('addon_master', $key);
				//$this->_redirect('/addon_console/panel');
			}
		}
	}

?>