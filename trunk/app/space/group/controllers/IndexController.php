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
			$list = $this->view->list;
			$select = DbModel::Space()->select()->from(array('gm' => 'tb_group_member'));
			switch ($list)
			{
				case 'my' : // 我的群组
				break;
				case 'friend' : // 好友的群组
				break;
				default : // 所有群组
				break;
			}
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