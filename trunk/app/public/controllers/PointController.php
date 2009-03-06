<?php

	/**
	 * 热度控制器
	 *
	 */
	class PointController extends Zend_Controller_Action 
	{	
		/**
		 * tab分配
		 *
		 */
		function indexAction()
		{
			if(Cmd::role() == 'guest') 
			$this->_forward('deny', 'error', 'public');
			$this->view->tab = $this->_getParam('tab', 'summary');
		}
		
		/**
		 * 自己的热心度摘要(排行，前后两位，全站总热度，百分比%)
		 *
		 */
		function summaryAction()
		{
			$point = Cmd::getSess('profile', 'point');
			$sum = Logic_Api::sumpoint('user');
			$percent = Logic_Api::percentpoint($point, $sum);
			$this->view->sumpoint = $sum;
			$this->view->mypoint = $point;
			$this->view->percent = $percent;
			$this->view->rank = Logic_Api::rankpoint($point, 'user');
			$this->view->neb = Logic_Api::nebpoint($point);
		}
		
		/**
		 * 近期热心度加分记录显示(全站/我的)
		 *
		 */
		function logAction()
		{
			
		}
		
		/**
		 * 赠送热心度
		 *
		 */
		function awardAction()
		{
			
		}
	}

?>