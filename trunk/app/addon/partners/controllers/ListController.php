<?php

	class Addon_Partners_ListController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 赞助企业列表显示
		 *
		 */
		function indexAction()
		{
			$uid = $this->params['uid'];
			$this->view->corps = Logic_Addon_Partners::getCorps($uid);
			$this->view->uid = $uid;
		}
	}

?>