<?php

	/**
	 * 群组俱乐部默认控制器
	 *
	 */
	class Space_Group_IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->list = $this->_getParam('list', 'my');
		}
		
		
		/**
		 * 我的群组列表
		 *
		 */
		function indexAction()
		{
			
		}
		
		/**
		 * 群组俱乐部功能块标签：我的群组，浏览群组，好友群组
		 *
		 */
		function tabAction()
		{
			
		}
	}

?>