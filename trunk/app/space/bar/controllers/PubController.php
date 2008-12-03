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
			$this->view->gp = $this->_getParam('gp', 0);
		}
		
		/**
		 * 话题
		 *
		 */
		function topicAction()
		{
			$this->view->headTitle('公共帖吧');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::topic($params);
				if(Alp_Sys::getMsg() == null)
				{
					$tid = Logic_Space_Bar_Topic::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 新闻
		 *
		 */
		function newsAction()
		{
			$this->view->headTitle('新闻快递');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::news($params);
				if(Alp_Sys::getMsg() == null)
				{
					$tid = Logic_Space_Bar_News::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
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