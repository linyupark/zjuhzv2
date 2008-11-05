<?php

	/**
	 * 附加组件的控制面板
	 *
	 */
	class Addon_Console_PanelController extends Zend_Controller_Action
	{
		private $params;
		
		function init()
		{
			$key = Cmd::getSess('addon_master');
			if($key != Zend_Registry::get('config')->addon_console->key)
			$this->_redirect('/addon_console/?key=');
			
			$this->params = $this->getRequest()->getParams();
			$this->view->mod_name = $this->_getParam('mod', 'partners');
		}
		
		function indexAction()
		{
			
		}
	}

?>