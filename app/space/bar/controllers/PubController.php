<?php

	/**
	 * 帖子发布入口
	 *
	 */
	class Space_Bar_PubController extends Zend_Controller_Action
	{
		function init()
		{
			// 是否为群组内发布
			$this->view->gp = $this->_getParam('gp');
		}
		
		/**
		 * 话题
		 *
		 */
		function topicAction()
		{
			
		}
		
		/**
		 * 新闻
		 *
		 */
		function newsAction()
		{}
		
		/**
		 * 活动
		 *
		 */
		function eventsAction()
		{}
		
		/**
		 * 投票
		 *
		 */
		function voteAction()
		{}
		
		/**
		 * 图片
		 *
		 */
		function photoAction()
		{}
		
		/**
		 * 共享
		 *
		 */
		function shareAction()
		{}
	} 

?>