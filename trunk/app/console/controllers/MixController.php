<?php

	/**
	 * 杂项管理
	 *
	 */
	class Console_MixController extends Zend_Controller_Action
	{
		function indexAction()
		{
			$this->view->tab = $this->_getParam('tab', 'player');
		}
		
		/**
		 * 首页播放管理
		 *
		 */
		function playerAction()
		{
			$file = $_SERVER['DOCUMENT_ROOT'].'/player/toppicxml.xml';
			if($this->getRequest()->isPost()) // 保存处理
			{
				$data = $this->getRequest()->getPost('xml');
				file_put_contents($file, stripslashes($data));
			}
			$xml = file_get_contents($file);
			$this->view->xml = $xml;
		}
	}
	
?>