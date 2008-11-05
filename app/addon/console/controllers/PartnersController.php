<?php

	class Addon_Console_PartnersController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		function indexAction()
		{
			// 罗列所有现有的企业信息
			$corps = Logic_Addon_Partners::getCorps();
			$pagination = Alp_Page::create(array(
				'href_open' => '<a href="?mod=partners&p=%d">',
				'href_close' => '</a>',
				'num_rows' => count($corps),
				'cur_page' => $this->_getParam('p', 1)
			));
			$this->view->corps = $corps;
			$this->pagination = $pagination;
		}
		
		/**
		 * 改变企业现有激活状态
		 *
		 */
		function changeactiveAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$corp = Logic_Addon_Partners::getCorp($this->params['cid']);
			$active = 0;
			if($corp['active'] == 0) $active = 1;
			try{
				DbModel::getSqlite('partners.s3db')->update('corporation', 
				array('active'=>$active), 'cid = '.(int)$this->params['cid']);
				echo $active;
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}

?>