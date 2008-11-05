<?php

	class Addon_Partners_ListController extends Zend_Controller_Action 
	{
		private $params;
		private $uid;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
			$this->uid = Cmd::getSess('addon_partner');
		}
		
		/**
		 * 赞助企业列表显示
		 *
		 */
		function indexAction()
		{
			$this->view->corps = Logic_Addon_Partners::getCorps($this->uid);
			if($this->uid == $this->params['uid'])
			$this->view->uid = $this->uid;
		}
	}

?>