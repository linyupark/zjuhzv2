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
		 * 群消息
		 *
		 */
		function msgAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Group::msg($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					Logic_Space_Group_Manage::msg($params);
					if(Alp_Sys::getMsg() == null){ echo 'success'; exit(); }
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 群标
		 *
		 */
		function logoAction()
		{
			
		}
		
		/**
		 * 基本信息
		 *
		 */
		function basicAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Group::create($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Group_Manage::basic($params);
					if(Alp_Sys::getMsg() == null){ echo 'success'; exit(); }
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		function tabAction()
		{
			$this->view->type = $this->_getParam('type', 'basic');
		}
	}

?>