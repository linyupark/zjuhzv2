<?php

	/**
	 * 群组主页控制器
	 *
	 */
	class Space_Group_HomeController extends Zend_Controller_Action 
	{		
		function init()
		{
			$this->view->gid = $this->_getParam('id');
		}
		
		/**
		 * 群组工具条
		 *
		 */
		function toolbarAction()
		{
			
		}
		
		/**
		 * 群组基本信息栏
		 *
		 */
		function infoAction()
		{
			$this->view->group = $this->_getParam('group');
		}
		
		/**
		 * 指定id的群组首页
		 *
		 */
		function indexAction()
		{
			$group = Logic_Space_Group::info($this->view->gid);
			if(Logic_Space_Group::isAllowedVisit($this->view->gid, Cmd::uid()))
			{
				$this->view->group = $group;
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
	}

?>