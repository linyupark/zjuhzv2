<?php

	class Api_Devote_GroupController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{ 
			$this->getRequest()->getParams(); 
			$this->getHelper('viewRenderer')->setNoRender(); 
		}
		
		/**
		 * 群组管理员可操作
		 *
		 */
		function masterAction()
		{
			// 核对身份
			if(Cmdv1::isGroupManager($this->params->gid) == true)
			{
				// 
			}
		}
	}

?>