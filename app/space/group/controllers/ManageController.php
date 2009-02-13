<?php

	/**
	 * 群组管理控制器
	 *
	 */
	class Space_Group_ManageController extends Zend_Controller_Action 
	{
		function init()
		{ 
			$gid = $this->_getParam('id');
			$this->view->group = Logic_Space_Group::info($gid);
			$this->view->gid = $gid; 
		}
		
		function indexAction()
		{
			$this->view->type = $this->_getParam('type', 'basic');
		}
		
		/**
		 * 基本信息
		 *
		 */
		function basicAction()
		{
			
		}
		
		function tabAction()
		{
			$this->view->type = $this->_getParam('type', 'basic');
		}
	}

?>