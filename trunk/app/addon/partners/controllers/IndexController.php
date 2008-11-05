<?php

	class Addon_Partners_IndexController extends Zend_Controller_Action 
	{
		private $params;
		private $uid;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
			$this->uid = Cmd::getSess('addon_partner');
		}
		
		function indexAction()
		{
			$corp = Logic_Addon_Partners::getCorp($this->params['cid']);
			if(($corp['active'] == 0 && $this->uid != $corp['uid']) || $corp == false)
			{
				$this->_redirect('/addon_partners/list');
			}
			if($this->uid == $corp['uid'])
			$this->view->manager = true;
			$this->view->headTitle($corp['name']);
			$this->view->corp = $corp;
		}
	}

?>