<?php

	/**
	 * 帖子附加功能控制器
	 *
	 */
	class Space_Bar_ToolbarController extends Zend_Controller_Action
	{	
		/**
		 * 顶帖子
		 *
		 */
		function rateAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$tid = $this->_getParam('tid');
				if(Logic_Space_Bar::rate($tid))
				echo 'success';
			}
		}
		
		/**
		 * 收藏帖子
		 *
		 */
		function favAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->_getAllParams();
				echo Logic_Space_Bar::fav($params, Cmd::uid());
			}
		}
	}

?>