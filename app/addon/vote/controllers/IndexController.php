<?php

	class Addon_Vote_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 页面获取的参数集合
		 *
		 * @var array
		 */
		private $params = array();
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		function indexAction()
		{
			$vid = (int)$this->params['vid'];
			if($vid > 0)
			{
				if($base = Logic_Addon_Vote::base($vid))
				{
					$this->view->base = $base;
					$this->view->options = Logic_Addon_Vote::options($vid);
					$this->view->total_rate = Logic_Addon_Vote::totalRate($vid);
				} 
				else echo Alp_Sys::jump('/addon_vote/?vid=0');
			}
			else $this->render('error');
		}
	}

?>