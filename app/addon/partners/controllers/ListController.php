<?php

	class Addon_Partners_ListController extends Zend_Controller_Action 
	{

		/**
		 * 赞助企业列表显示(已经激活)
		 *
		 */
		function indexAction()
		{
			$this->view->corps = Logic_Addon_Partners::getCorps($this->uid);
		}
	}

?>