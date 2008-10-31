<?php

	class Addon_Partners_ManageController extends Zend_Controller_Action 
	{
		private $params;
		private $uid;
		
		function init()
		{
			// 身份审核
			$uid = Cmd::getSess('addon_partner');
			if($uid == null)
			$this->_redirect('/addon_partners/user');
			else $this->uid = $uid;
			
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 建立新企业
		 *
		 */
		function corpcreateAction()
		{
			$data = Cmd::getSess('addon_partner_setup');
			$this->view->step = (int)$data['step'];
			
		}
	}

?>