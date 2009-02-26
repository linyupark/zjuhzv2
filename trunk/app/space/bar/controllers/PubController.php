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
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Topic::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_topic'
						));
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
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_News::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_news'
						));
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 互助
		 *
		 */
		function helpAction()
		{
			$this->view->headTitle('校友互助');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::help($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Help::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_help'
						));
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
		{
			$this->view->headTitle('校友活动');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::events($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Events::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_events'
						));
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 投票
		 *
		 */
		function voteAction()
		{
			$this->view->headTitle('投票调查');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::vote($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Vote::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_vote'
						));
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 图片
		 *
		 */
		function photoAction()
		{
			$this->view->headTitle('看图论事');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::photo($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Photo::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_photo'
						));
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 共享
		 *
		 */
		function shareAction()
		{
			$this->view->headTitle('文件共享');
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::share($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					$tid = Logic_Space_Bar_Share::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::bar(array(
							'uid' => $params['uid'],
							'gid' => $params['group'],
							'tid' => $tid,
							'key' => 'add_share'
						));
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
	} 

?>